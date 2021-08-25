<?php
namespace Concrete\Theme\Atomik;

use Concrete\Core\Feature\Features;
use Concrete\Core\Page\Theme\BedrockThemeTrait;
use Concrete\Core\Page\Theme\Color\Color;
use Concrete\Core\Page\Theme\Color\ColorCollection;
use Concrete\Core\Page\Theme\Documentation\DocumentationProvider;
use Concrete\Core\Page\Theme\Documentation\DocumentationProviderInterface;
use Concrete\Core\Page\Theme\Documentation\ThemeDocumentationPage;
use Concrete\Core\Page\Theme\Theme;

class PageTheme extends Theme
{
    
    use BedrockThemeTrait;

    use BedrockThemeTrait {
        getColorCollection as getBedrockColorCollection;
    }

    public function getThemeName()
    {
        return t('Atomik');
    }

    public function getThemeDescription()
    {
        return t('A Concrete CMS theme built for 2021.');
    }

    public function getThemeSupportedFeatures()
    {
        return [
            Features::BASICS,
            Features::TYPOGRAPHY,
            Features::CALENDAR,
            Features::CONVERSATIONS,
            Features::FAQ,
            Features::NAVIGATION,
            Features::IMAGERY,
            Features::FORMS,
            Features::SEARCH,
            Features::TESTIMONIALS,
            Features::TAXONOMY,
        ];
    }

    /**
     * @return array
     */
    public function getThemeResponsiveImageMap()
    {
        return [
            'xs' => '0',
            'sm' => '576px',
            'md' => '768px',
            'lg' => '992px',
            'xl' => '1200px',
        ];
    }

    public function getDocumentationProvider(): ?DocumentationProviderInterface
    {
        $pages = [
            new ThemeDocumentationPage($this, 'Overview', 'overview.xml'),
        ];
        $pages = array_merge($pages, $this->getDocumentationPages());
        return DocumentationProvider::createFromArray($pages);
    }

    public function getColorCollection(): ?ColorCollection
    {
        $collection = $this->getBedrockColorCollection();
        $collection->add(new Color('light-accent', t('Light Accent')));
        $collection->add(new Color('accent', t('Accent')));
        $collection->add(new Color('dark-accent', t('Dark Accent')));
        return $collection;
    }


}
