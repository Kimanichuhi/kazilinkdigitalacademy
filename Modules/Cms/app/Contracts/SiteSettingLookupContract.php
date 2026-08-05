<?php

namespace Modules\Cms\Contracts;

interface SiteSettingLookupContract
{
    /**
     * @param  list<string>  $keys
     * @return array<string, string>
     */
    public function getMany(array $keys): array;
}
