<?php

/* layout.html */
class __TwigTemplate_a6d63afa91556f30dbdeb724c470bc29e71bbf3f1152c5ef75cb7969dde18cac extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<html>
<header>header</header>
<content>
    ";
        // line 4
        $this->displayBlock('content', $context, $blocks);
        // line 6
        echo "</content>
<footer>
    footer
</footer>
</html>";
    }

    // line 4
    public function block_content($context, array $blocks = array())
    {
        // line 5
        echo "    ";
    }

    public function getTemplateName()
    {
        return "layout.html";
    }

    public function getDebugInfo()
    {
        return array (  38 => 5,  35 => 4,  27 => 6,  25 => 4,  20 => 1,);
    }
}
/* <html>*/
/* <header>header</header>*/
/* <content>*/
/*     {% block content%}*/
/*     {% endblock content%}*/
/* </content>*/
/* <footer>*/
/*     footer*/
/* </footer>*/
/* </html>*/
