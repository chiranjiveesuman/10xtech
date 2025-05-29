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

/* core/themes/olivero/templates/form/fieldset.html.twig */
class __TwigTemplate_93f54314dacdf5d7a5b7a0411a278805 extends Template
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
        // line 33
        $context["classes"] = ["fieldset", ((CoreExtension::getAttribute($this->env, $this->source,         // line 35
($context["attributes"] ?? null), "hasClass", ["fieldgroup"], "method", false, false, true, 35)) ? ("fieldset--group") : ("")), "js-form-item", "form-item", "js-form-wrapper", "form-wrapper"];
        // line 43
        $context["wrapper_classes"] = ["fieldset__wrapper", ((CoreExtension::getAttribute($this->env, $this->source,         // line 45
($context["attributes"] ?? null), "hasClass", ["fieldgroup"], "method", false, false, true, 45)) ? ("fieldset__wrapper--group") : (""))];
        // line 49
        $context["legend_span_classes"] = ["fieldset__label", ((CoreExtension::getAttribute($this->env, $this->source,         // line 51
($context["attributes"] ?? null), "hasClass", ["fieldgroup"], "method", false, false, true, 51)) ? ("fieldset__label--group") : ("")), ((        // line 52
($context["required"] ?? null)) ? ("js-form-required") : ("")), ((        // line 53
($context["required"] ?? null)) ? ("form-required") : (""))];
        // line 57
        $context["legend_classes"] = ["fieldset__legend", (((CoreExtension::getAttribute($this->env, $this->source,         // line 59
($context["attributes"] ?? null), "hasClass", ["fieldgroup"], "method", false, false, true, 59) &&  !CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "hasClass", ["form-composite"], "method", false, false, true, 59))) ? ("fieldset__legend--group") : ("")), ((CoreExtension::getAttribute($this->env, $this->source,         // line 60
($context["attributes"] ?? null), "hasClass", ["form-composite"], "method", false, false, true, 60)) ? ("fieldset__legend--composite") : ("")), (((        // line 61
($context["title_display"] ?? null) == "invisible")) ? ("fieldset__legend--invisible") : ("fieldset__legend--visible"))];
        // line 65
        $context["description_classes"] = ["fieldset__description"];
        // line 69
        yield "
<fieldset";
        // line 70
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [($context["classes"] ?? null)], "method", false, false, true, 70), 70, $this->source), "html", null, true);
        yield ">
  ";
        // line 72
        yield "  ";
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["legend"] ?? null), "title", [], "any", false, false, true, 72)) {
            // line 73
            yield "  <legend";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["legend"] ?? null), "attributes", [], "any", false, false, true, 73), "addClass", [($context["legend_classes"] ?? null)], "method", false, false, true, 73), 73, $this->source), "html", null, true);
            yield ">
    <span";
            // line 74
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["legend_span"] ?? null), "attributes", [], "any", false, false, true, 74), "addClass", [($context["legend_span_classes"] ?? null)], "method", false, false, true, 74), 74, $this->source), "html", null, true);
            yield ">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["legend"] ?? null), "title", [], "any", false, false, true, 74), 74, $this->source), "html", null, true);
            yield "</span>
  </legend>
  ";
        }
        // line 77
        yield "
  <div";
        // line 78
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["content_attributes"] ?? null), "addClass", [($context["wrapper_classes"] ?? null)], "method", false, false, true, 78), 78, $this->source), "html", null, true);
        yield ">
    ";
        // line 79
        if (($context["inline_items"] ?? null)) {
            // line 80
            yield "      <div class=\"container-inline\">
    ";
        }
        // line 82
        yield "
    ";
        // line 83
        if (((($context["description_display"] ?? null) == "before") && CoreExtension::getAttribute($this->env, $this->source, ($context["description"] ?? null), "content", [], "any", false, false, true, 83))) {
            // line 84
            yield "      <div";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["description"] ?? null), "attributes", [], "any", false, false, true, 84), "addClass", [($context["description_classes"] ?? null)], "method", false, false, true, 84), 84, $this->source), "html", null, true);
            yield ">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["description"] ?? null), "content", [], "any", false, false, true, 84), 84, $this->source), "html", null, true);
            yield "</div>
    ";
        }
        // line 86
        yield "    ";
        if (($context["prefix"] ?? null)) {
            // line 87
            yield "      <span class=\"fieldset__prefix\">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["prefix"] ?? null), 87, $this->source), "html", null, true);
            yield "</span>
    ";
        }
        // line 89
        yield "    ";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["children"] ?? null), 89, $this->source), "html", null, true);
        yield "
    ";
        // line 90
        if (($context["suffix"] ?? null)) {
            // line 91
            yield "      <span class=\"fieldset__suffix\">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["suffix"] ?? null), 91, $this->source), "html", null, true);
            yield "</span>
    ";
        }
        // line 93
        yield "    ";
        if (($context["errors"] ?? null)) {
            // line 94
            yield "      <div class=\"fieldset__error-message\">
        ";
            // line 95
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["errors"] ?? null), 95, $this->source), "html", null, true);
            yield "
      </div>
    ";
        }
        // line 98
        yield "    ";
        if ((CoreExtension::inFilter(($context["description_display"] ?? null), ["after", "invisible"]) && CoreExtension::getAttribute($this->env, $this->source, ($context["description"] ?? null), "content", [], "any", false, false, true, 98))) {
            // line 99
            yield "      <div";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["description"] ?? null), "attributes", [], "any", false, false, true, 99), "addClass", [($context["description_classes"] ?? null)], "method", false, false, true, 99), 99, $this->source), "html", null, true);
            yield ">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["description"] ?? null), "content", [], "any", false, false, true, 99), 99, $this->source), "html", null, true);
            yield "</div>
    ";
        }
        // line 101
        yield "
    ";
        // line 102
        if (($context["inline_items"] ?? null)) {
            // line 103
            yield "      </div>
    ";
        }
        // line 105
        yield "  </div>
</fieldset>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["attributes", "required", "title_display", "legend", "legend_span", "content_attributes", "inline_items", "description_display", "description", "prefix", "children", "suffix", "errors"]);        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "core/themes/olivero/templates/form/fieldset.html.twig";
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
        return array (  161 => 105,  157 => 103,  155 => 102,  152 => 101,  144 => 99,  141 => 98,  135 => 95,  132 => 94,  129 => 93,  123 => 91,  121 => 90,  116 => 89,  110 => 87,  107 => 86,  99 => 84,  97 => 83,  94 => 82,  90 => 80,  88 => 79,  84 => 78,  81 => 77,  73 => 74,  68 => 73,  65 => 72,  61 => 70,  58 => 69,  56 => 65,  54 => 61,  53 => 60,  52 => 59,  51 => 57,  49 => 53,  48 => 52,  47 => 51,  46 => 49,  44 => 45,  43 => 43,  41 => 35,  40 => 33,);
    }

    public function getSourceContext()
    {
        return new Source("", "core/themes/olivero/templates/form/fieldset.html.twig", "C:\\xampp\\htdocs\\install-dir\\web\\core\\themes\\olivero\\templates\\form\\fieldset.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 33, "if" => 72);
        static $filters = array("escape" => 70);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
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
