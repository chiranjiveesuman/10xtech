<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* @style_guide/person-card.html.twig */
class __TwigTemplate_465e0210a7a7dea982ef8c975378756c extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension(SandboxExtension::class);
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 16
        yield "<div class=\"bg-white rounded-lg shadow-sm overflow-hidden max-w-sm border border-gray-100\">
  <div class=\"flex flex-col items-center pt-8 px-6 pb-6\">
    <div class=\"w-32 h-32 mb-4\">
      <img src=\"";
        // line 19
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["image"] ?? null), 19, $this->source), "html", null, true);
        yield "\" alt=\"";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["name"] ?? null), 19, $this->source), "html", null, true);
        yield "\" class=\"w-full h-full rounded-full object-cover\">
    </div>
    <h3 class=\"text-gray-800 text-lg font-medium mb-1\">";
        // line 21
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["name"] ?? null), 21, $this->source), "html", null, true);
        yield "</h3>
    <p class=\"text-gray-500 text-sm\">";
        // line 22
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["position"] ?? null), 22, $this->source), "html", null, true);
        yield "</p>
    ";
        // line 23
        if ((array_key_exists("admin", $context) && ($context["admin"] ?? null))) {
            // line 24
            yield "      <span class=\"mt-3 px-3 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-full\">Admin</span>
    ";
        }
        // line 26
        yield "  </div>
  <div class=\"grid grid-cols-2 divide-x border-t border-gray-100\">
    <a href=\"mailto:";
        // line 28
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["email"] ?? null), 28, $this->source), "html", null, true);
        yield "\" class=\"flex items-center justify-center py-4 text-gray-600 hover:bg-gray-50\">
      <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"h-5 w-5 text-gray-400 mr-2\" viewBox=\"0 0 20 20\" fill=\"currentColor\">
        <path d=\"M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z\" />
        <path d=\"M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z\" />
      </svg>
      <span>Email</span>
    </a>
    <a href=\"tel:";
        // line 35
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["phone"] ?? null), 35, $this->source), "html", null, true);
        yield "\" class=\"flex items-center justify-center py-4 text-gray-600 hover:bg-gray-50\">
      <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"h-5 w-5 text-gray-400 mr-2\" viewBox=\"0 0 20 20\" fill=\"currentColor\">
        <path d=\"M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z\" />
      </svg>
      <span>Call</span>
    </a>
  </div>
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["image", "name", "position", "admin", "email", "phone"]);        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "@style_guide/person-card.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  80 => 35,  70 => 28,  66 => 26,  62 => 24,  60 => 23,  56 => 22,  52 => 21,  45 => 19,  40 => 16,);
    }

    public function getSourceContext()
    {
        return new Source("", "@style_guide/person-card.html.twig", "C:\\xampp\\htdocs\\install-dir\\web\\modules\\custom\\style_guide\\templates\\person-card.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 23);
        static $filters = array("escape" => 19);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape'],
                [],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
