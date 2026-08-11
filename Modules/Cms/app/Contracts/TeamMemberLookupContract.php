<?php

namespace Modules\Cms\Contracts;

interface TeamMemberLookupContract
{
    /**
     * The active team member flagged as the company's founder, for the
     * homepage "Welcome from the Founder" section.
     */
    public function findFounder(): ?array;
}
