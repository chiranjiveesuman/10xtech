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

/* modules/custom/style_guide/templates/style-guide.html.twig */
class __TwigTemplate_9b0a717ed1d2b2bd451b1caef48e55de extends Template
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
        // line 13
        yield "<div class=\"min-h-screen bg-gray-50 flex flex-col items-center justify-center px-4 py-10\">
  <h1 class=\"text-3xl font-bold text-gray-900 mb-2\">Style Guide</h1>
  <h2 class=\"text-xl font-semibold text-gray-800 mb-4\">Person Card</h2>

  ";
        // line 17
        yield from         $this->loadTemplate("@style_guide/person-card.html.twig", "modules/custom/style_guide/templates/style-guide.html.twig", 17)->unwrap()->yield(CoreExtension::merge($context, ["name" => CoreExtension::getAttribute($this->env, $this->source,         // line 18
($context["person"] ?? null), "name", [], "any", false, false, true, 18), "position" => CoreExtension::getAttribute($this->env, $this->source,         // line 19
($context["person"] ?? null), "position", [], "any", false, false, true, 19), "image" => CoreExtension::getAttribute($this->env, $this->source,         // line 20
($context["person"] ?? null), "image", [], "any", false, false, true, 20), "email" => CoreExtension::getAttribute($this->env, $this->source,         // line 21
($context["person"] ?? null), "email", [], "any", false, false, true, 21), "phone" => CoreExtension::getAttribute($this->env, $this->source,         // line 22
($context["person"] ?? null), "phone", [], "any", false, false, true, 22), "admin" => CoreExtension::getAttribute($this->env, $this->source,         // line 23
($context["person"] ?? null), "admin", [], "any", false, false, true, 23)]));
        // line 25
        yield "
  <div class=\"mt-16 w-full max-w-7xl\">
    <h2 class=\"text-2xl font-bold mb-6 text-gray-800\">Person Cards Grid</h2>
    <div class=\"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6\">
      ";
        // line 29
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["persons"] ?? null));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["person"]) {
            // line 30
            yield "        ";
            yield from             $this->loadTemplate("@style_guide/person-card.html.twig", "modules/custom/style_guide/templates/style-guide.html.twig", 30)->unwrap()->yield(CoreExtension::merge($context, ["name" => CoreExtension::getAttribute($this->env, $this->source,             // line 31
$context["person"], "name", [], "any", false, false, true, 31), "position" => CoreExtension::getAttribute($this->env, $this->source,             // line 32
$context["person"], "position", [], "any", false, false, true, 32), "image" => CoreExtension::getAttribute($this->env, $this->source,             // line 33
$context["person"], "image", [], "any", false, false, true, 33), "email" => CoreExtension::getAttribute($this->env, $this->source,             // line 34
$context["person"], "email", [], "any", false, false, true, 34), "phone" => CoreExtension::getAttribute($this->env, $this->source,             // line 35
$context["person"], "phone", [], "any", false, false, true, 35), "admin" => CoreExtension::getAttribute($this->env, $this->source,             // line 36
$context["person"], "admin", [], "any", false, false, true, 36)]));
            // line 38
            yield "      ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['person'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 39
        yield "    </div>
  </div>
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["person", "persons"]);        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "modules/custom/style_guide/templates/style-guide.html.twig";
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
        return array (  100 => 39,  86 => 38,  84 => 36,  83 => 35,  82 => 34,  81 => 33,  80 => 32,  79 => 31,  77 => 30,  60 => 29,  54 => 25,  52 => 23,  51 => 22,  50 => 21,  49 => 20,  48 => 19,  47 => 18,  46 => 17,  40 => 13,);
    }

    public function getSourceContext()
    {
        return new Source("", "modules/custom/style_guide/templates/style-guide.html.twig", "C:\\xampp\\htdocs\\install-dir\\web\\modules\\custom\\style_guide\\templates\\style-guide.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("include" => 17, "for" => 29);
        static $filters = array();
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['include', 'for'],
                [],
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
