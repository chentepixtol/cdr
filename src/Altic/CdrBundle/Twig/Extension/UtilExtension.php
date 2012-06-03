<?php

namespace Altic\CdrBundle\Twig\Extension;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use CG\Core\ClassUtils;

class UtilExtension extends \Twig_Extension
{
    protected $loader;

    public function __construct(FilesystemLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'fc' => new \Twig_Function_Method($this, 'fc', array('is_safe' => array('html'))),
        );
    }


    /**
     *
     * @param unknown_type $xml
     * @param unknown_type $baseUrl
     * @param unknown_type $type
     * @param unknown_type $width
     * @param unknown_type $height
     * @return string
     */
    public function fc($xml, $swf, $width = '720', $height = '340'){
        $xml = preg_replace('/\s/', ' ', $xml);
        $id = md5(microtime());
        $dir = dirname($swf);
        $html = <<< EOF
        <div id="fc{$id}" class="center" style="position: relative; z-index: 0;"></div>
<div id="fcexp{$id}" class="center">FusionCharts Export Handler Component</div>
<script type="text/javascript">
    $(document).ready(function(){
        var chart = new FusionCharts("{$swf}", "{$id}", "{$width}", "{$height}", "0", "1");
        chart.setDataXML('{$xml}');
        chart.addParam("WMode", "Transparent");
        chart.render("fc{$id}");

        var myExportComponent = new FusionChartsExportObject("exporter{$id}", "{$dir}/FCExporter.swf");

        myExportComponent.Render("fcexp{$id}");
   });
</script>
EOF;
        return $html;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'util';
    }
}
