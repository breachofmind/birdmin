<?php

namespace Birdmin;

use Birdmin\Collections\MediaCollection;
use Birdmin\Core\Model;
use Birdmin\Http\Requests\Request;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class Media extends Model
{
    public static $MIMETYPES = [];

    protected $model;

    protected $fillable = [
        'title',
        'alt_text',
        'caption',
        'category',
        'metadata',
    ];

    protected $guarded = [
        'file_name',
        'file_size',
        'file_type',
        'etag',
    ];

    protected $searchable = ['title','file_name','alt_text','file_type','category'];

    protected $appends = ['url', 'sizes'];


    public $timestamps = true;
    public $dates = ['created_at','updated_at'];

    protected $table = 'media';


    /**
     * Register the mimetypes in this object.
     * Set the default base path.
     */
    public static function boot ()
    {
        parent::boot();

        $map    = config('media.map');
        $types  = config('media.types');
        foreach ($types as $name=>$extensions) {

            static::$MIMETYPES[$name] = [];
            foreach ($extensions as $ext) {
                if (!array_key_exists($ext,$map)) {
                    continue;
                }
                static::$MIMETYPES[$name][$ext] = $map[$ext];
            }

        }

    }

    /**
     * Get the primary url to the original uploaded file.
     * @return string
     */
    public function getUrlAttribute ()
    {
        return $this->url();
    }

    /**
     * Return an array urls to available image sizes, if they exist.
     * @return array|null
     */
    public function getSizesAttribute ()
    {
        $sizes = array();
        if (!$this->isImage()) {
            return null;
        }
        foreach (config('media.crops') as $size=>$callable) {
            if ($this->exists($size)) {
                $sizes[$size] = $this->url($size);
            }
        }
        return $sizes;
    }

    /**
     * Return the base path for all media.
     * @param null|string $path
     * @return string
     */
    public static function basePath ($path=null)
    {
        return config('media.upload_path').($path?"/{$path}/":"/");
    }

    /**
     * Return the base URL for all media.
     * @param null|string $path
     * @param bool|true $relative
     * @return string
     */
    public static function baseUrl ($path=null, $relative=true)
    {
        $path = config('media.upload_url').($path?"/{$path}/":"/");
        $url = config('app.url');
        return $relative ? $path : $url.$path;
    }

    /**
     * Return the server path to the file.
     * @param null $path
     * @return string
     */
    public function path ($path=null)
    {
        if ($this->isDocument() && !$path) {
            $path = "documents";
        }
        return Media::basePath($path).$this->file_name;
    }

    /**
     * Return the URL path to the file.
     * @param null|string $path
     * @param bool|true $relative path
     * @return string
     */
    public function url ($path=null, $relative=false)
    {
        if ($this->isDocument() && !$path) {
            $path = "documents";
        }
        return Media::baseUrl($path, $relative).$this->file_name;
    }

    /**
     * Alias for url(), only returns a relative link.
     * @param null|string $path
     * @return string
     */
    public function href ($path=null)
    {
        return $this->url($path,true);
    }

    /**
     * Check if the file exists on the filesystem.
     * @param null|string $directory
     * @return bool
     */
    public function exists ($directory=null)
    {
        return file_exists( $this->path($directory) );
    }

    /**
     * Check if this is of the given type group (image,document,etc)
     * @param string $name
     * @return bool
     */
    public function isType ($name)
    {
        return in_array($this->file_type, static::$MIMETYPES[$name]);
    }

    /**
     * Check if this is an image type.
     * @return bool
     */
    public function isImage ()
    {
        return $this->isType('image');
    }

    /**
     * Check if this is a document type.
     * @return bool
     */
    public function isDocument ()
    {
        return $this->isType('document');
    }

    /**
     * Check if this object is a duplicate of another by finding the md5 hash.
     * Returns the first matching object or null.
     * @return Media|null
     */
    public function isDuplicate ()
    {
        return Media::where('etag',$this->etag)
            ->where('id','!=',$this->id)->get()->first();
    }


    /**
     * Return the human-readable native type, such as 'image' or 'document'.
     * @return int|string
     */
    public function nativeType()
    {
        foreach (static::$MIMETYPES as $name=>$types) {
            if (in_array($this->file_type,$types)) {
                return $name;
            }
        }
        // Doesn't really have a native type assigned.
        return "media";
    }

    /**
     * Return the image html tag for this object in the given size/crop.
     * Images naturally return the file, other file types may return defaults.
     * @param string $size
     * @param string|array $classes
     * @param array $attrs
     * @return string
     */
    public function img ($size=null, $classes=null, $attrs=[])
    {
        $attr = [
            'src' => $this->urlByType($size),
            'class' => is_array($classes) ? join(" ",$classes) : $classes,
            'alt' => empty($this->alt_text) ? $this->title : $this->alt_text,
        ];
        return attributize(array_merge($attr,$attrs),'img');
    }

    /**
     * Return an image url based on this object's file type.
     * @param null|string $size
     * @return string
     */
    public function urlByType ($size=null)
    {
        // Images always return the actual request.
        if ($this->isImage()) {
            return $this->href($size);
        }
        // Documents or other types return a placeholder.
        return config('app.url')."/public/img/icon-document.png";
    }

    /**
     * Return an anchor to this media.
     * @param null|string $size
     * @param null|string $classes
     * @return string
     */
    public function anchor ($size=null, $classes=null)
    {
        $fileUrl = $this->url(null);
        return "<a href=\"$fileUrl\">".$this->img($size,$classes)."</a>";
    }

    /**
     * Import the given file into the system.
     * @param $file string path to file
     * @return boolean
     */
    public static function import ($file, $move=true)
    {
        $explode = explode(".",$file);
        $ext = array_pop($explode);
        $mimetype = lookup_mimetype($ext);

        // Note - 'test' is true so the file is not treated like uploaded file.
        $upload = new UploadedFile($file, basename($file), $mimetype, filesize($file),UPLOAD_ERR_OK,true);
        return Media::upload($upload,$move);
    }

    /**
     * Perform a file upload.
     * @param UploadedFile $file
     * @param $move boolean - move or copy the file?
     * @return \Exception|FileException|Media
     */
    public static function upload (UploadedFile $file, $move=true)
    {
        Media::unguard();
        $media = new Media([
            'file_type' => $file->getClientMimeType(),
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'title' => basename($file->getClientOriginalName(), ".".$file->getClientOriginalExtension()),

        ]);

        // Don't overwrite the file. Give it a new name.
        $media->file_name = Media::getUniqueName( $media->path() );

        try {
            if ($move) $file->move(dirname($media->path()), $media->file_name);
            else copy($file->getPathname(), $media->path());

        } catch (FileException $error) {
            return $error;
        }

        $media->etag = md5_file($media->path());
        $media->save();

        // Create cropped image sizes, if any specified.
        foreach (config('media.crops') as $directory=>$callable) {
            $media->generate($directory,$callable);
        }
        Media::reguard();
        return $media;
    }

    /**
     * Return a unique file name for a file at the given path.
     * @param $path string
     * @return string
     */
    public static function getUniqueName ($path)
    {
        $n=0;
        $dirname = dirname($path);
        $exploded = explode(".",basename($path));
        $ext = array_pop($exploded);
        $filename = join(".",$exploded);

        while (file_exists($path)) {
            $n++;
            $path = $dirname."/".$filename."-$n".".$ext";
        }
        return basename($path);
    }

    /**
     * Generate a new manipulated file, such as a crop.
     * @param $name string directory
     * @param callable $callable  ($image)
     * @return \Exception|null|int
     */
    public function generate ($name, callable $callable)
    {
        if (!$this->isImage()) {
            return null;
        }
        $path = Media::basePath($name);
        if (!file_exists($path)) {
            mkdir($path, 0755);
        }
        try {
            $img = Image::make($this->path());
            $callable($img);
        } catch(\Exception $error) {
            return $error;
        }

        return $img ? $img->save ($path.$this->file_name) : null;
    }


    /**
     * Delete the media from the database and from the server.
     * @return bool
     * @throws \Exception
     */
    public function delete ()
    {
        // Delete any files, including sizes.
        try {
            if ($this->sizes) {
                foreach ($this->sizes as $key=>$url) {
                    unlink($this->path($key));
                }
            }
            unlink($this->path());
        } catch(\ErrorException $e) {
            // File probably doesn't exist, so delete anyway.
        }


        Relationship::clear($this);

        return parent::delete();
    }

    /**
     * Custom Media collection.
     * @param array $models
     * @return MediaCollection
     */
    public function newCollection (array $models=[])
    {
        return new MediaCollection($models);
    }
}
