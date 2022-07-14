# url-bundle
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