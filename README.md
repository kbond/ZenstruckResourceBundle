# ZenstruckResourceBundle

**NOTE:** This bundle is under heavy development, **use at your own risk**

Provides an easy way to create a RESTful CRUD for your entities.

## Full Default Configuration

```yaml
zenstruck_resource:
    default_controller_class:  Zenstruck\ResourceBundle\Controller\ResourceController
    controller_utils_class:  Zenstruck\ResourceBundle\Controller\ControllerUtil
    controllers:

        # Prototype
        name:

            # The entity (in the short notation) to create a resource controller for.
            entity:               ~ # Required, Example: AppBundle:Product

            # The optional FQN of the resource controller. The above "default_controller_class" option will be used if left blank.
            controller_class:     ~

            # The service id for the generated controller. By default it is: "<bundle_prefix>.controller.<resource_name>".
            controller_id:        ~

            # The optional FQN of the form. It defaults to the Symfony2 standard for the resource.
            form_class:           ~

            # The default route to use after create/edit/delete actions.  Defaults to the "list" action if enabled or "homepage" if not.
            default_route:        ~

            # none: disables permission checking, simple: checks for ROLE_<NAME>_ADMIN on new/edit/delete actions, full: checks for ROLE_<NAME>_<ACTION> on new/edit/delete actions.
            permissions:          none

            routing:
                enabled:              false

                # An array of disabled actions. Allowed values: list, show, new, post, edit, put, delete.
                disabled_actions:     [] # Example: [show, list]
                prefix:               /
                default_format:       html
                formats:              html # Example: html|json

                # Additional routes for this resource.
                extra_routes:

                    # Examples:
                    promote:
                        pattern:             /promote
                        methods:             POST
                    photos:
                        pattern:             /{id}/photos

                    # Prototype
                    name:
                        pattern:              ~ # Required
                        methods:              GET
                        formats:              html
                        default_format:       html

            # The grid option is available when ZenstruckDataGridBundle is installed.
            grid:
                enabled:              false

                # The service id for the generated grid. By default it is: "<bundle_prefix>.grid.<grid_name>".
                service_id:           ~

                # Customize the grid executor (must implement ExecutorInterface)
                executor_service:     ~

                # Whether or not to use a paginated grid.
                paginated:            true
                fields:

                    # Prototype
                    name:
                        label:                ~

                        # Set false to hide on display (can still be filtered/sorted)
                        visible:              true
                        filterable:           false
                        filter_value:         ~
                        sortable:             false
                        sort_direction:       ASC
                        format:               ~
                        align:                ~
                        default:              ~
```