<?php

namespace Config;

use App\Services\BlogService;
use App\Services\PropertyService;
use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */

    /**
     * Property dataset service (reads/filters/sorts public/data/properties.json).
     */
    public static function property(bool $getShared = true): PropertyService
    {
        if ($getShared) {
            return static::getSharedInstance('property');
        }

        return new PropertyService();
    }

    /**
     * Blog dataset service (reads/queries public/data/blog.json).
     */
    public static function blog(bool $getShared = true): BlogService
    {
        if ($getShared) {
            return static::getSharedInstance('blog');
        }

        return new BlogService();
    }
}
