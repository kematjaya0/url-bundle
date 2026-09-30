# url-bundle

Supports symfony 5.4 (PHP >= 7.4) and 6.4 (PHP >= 8.1). For symfony 7 use version 7.x.

1. Installation
    ```
    composer require kematjaya/url-bundle
    ```
2. dump available route path
    ```
    php bin/console url:configure
    ```
    default will update on file 'resources/url.yaml'
3. usage
    ```
    {{ link_to('route_path', {id: data.id}, {class: "btn btn-xs btn-outline-info", 'icon': '<span class="fa fa-edit"></span>', label: 'edit'|trans}, {action: 'update', object: data}) }}
    {{ delete_tag('delete' ~ data.id, 'route_path', {id: data.id}, {class: "btn btn-xs btn-outline-danger", 'icon': '<span class="fa fa-trash"></span>', label: 'delete'|trans}, {action: 'delete', object: data}) }}
    ```
   4. configation (optional)
      ```
      url:
         resources_dir: '%kernel.project_dir%/resources'
         resources_file: 'url.yaml'
         whitelist: []
      ```
5. run the tests in docker (PHP x Symfony matrix)
    ```
    sh ../test.sh url-bundle 7.4 5.4   # PHP 7.4 + Symfony 5.4
    sh ../test.sh url-bundle 8.1 6.4   # PHP 8.1 + Symfony 6.4
    sh ../test.sh url-bundle all      # PHP 8.1 & 8.3 + Symfony 6.4
    ```
