<?php

namespace Markup\NeedleBundle\Provider;

/**
 * An interface for a provider object that can provide facet objects.
 **/
interface FacetProviderInterface
{
    /**
     * Gets a facet object using a name.  Returns null if name does not correspond to known facet.
     *
     * @param  string                                             $name
     * @return \Markup\NeedleBundle\Facet\FacetInterface|null
     **/
    public function getFacetByName($name);
}
