<?php


namespace Core;


class View
{
    /**
     * Render a view file
     *
     * @param string $view  The view file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function render($view, $args = [])
    {
        //base_page is template/layout, will be included in views
        $base_page = dirname(__DIR__) . "/App/Views/base.php";
        extract($args, EXTR_SKIP);

        $file = "../App/Views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
//            echo "$file not found";
            throw new \Exception("$file not found");
        }
    }

}