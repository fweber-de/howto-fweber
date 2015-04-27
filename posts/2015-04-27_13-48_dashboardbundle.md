---
title: DashboardBundle
slug: dashboardbundle
date: 2015-04-27
time: 13-48-10
---
`Dashboards` im DashboardBundle verstehen sich als Sammlung von individuellen `Widgets`, welche Daten aus einer Datenquelle darstellen.  
`Dashboards` und `Widgets` werden als PHP-Klassen definiert und über die `DashboardFactory` geladen und zur Verfügung gestellt.

## Tutorial

## Dashboard Klasse erstellen

In einem Bundle der Symfony App muss ein Ordner `Dashboard` erstellt werden, in welchem die Datei `ManagementDashboard.php` erstellt wird.  
Ebenfalls muss im Ordner `Widget` eine Klasse `BAInProduktionWidget.php` erstellt werden.

```php
<?php

namespace Ligneus\AppBundle\Dashboard;

use Ligneus\DashboardBundle\Dashboard\AbstractDashboard;

class ManagementDashboard extends AbstractDashboard
{
    public function __construct($odbc)
    {
        parent::__construct($odbc);

        $this->widgets
                ->add(new \Ligneus\AppBundle\Widget\BAInProduktionWidget())
        ;
    }

    public function getName()
    {
        return 'ManagementDashboard';
    }
}
```

```php
<?php

namespace Ligneus\AppBundle\Widget;

use Ligneus\DashboardBundle\Widget\AbstractWidget;

class BAInProduktionWidget extends AbstractWidget
{
    /**
     * @var int
     */
    public $numberOfBAInProduction;

    public function getName()
    {
        return "BAInProductionWidget";
    }

    public function collectData($odbc)
    {
        $this->numberOfBAInProduction = $odbc->executeQuery(sprintf('
            select  COUNT(*)
            from    AUF_STAMM
            where   (AUF_STATUS_ID = 5 or AUF_STATUS_ID = 9)
                and AUF_TYP_ID in (5, 6, 7, 8)
        '));

        return $this;
    }
}
```

In einer Controller-Action kann dann durch Aufruf von

```php
$data = $this->get('app.dashboard_factory')->get('Management')->collectData();
```

die Datensammlung angestoßen werden.  

`$data` enthält dann folgende Struktur:

```
ManagementDashboard {▼
  #widgets: WidgetCollection {▼
    #elements: array:1 [▼
      0 => BAInProduktionWidget {#637 ▼
        +numberOfBAInProduction: "859"
      }
    ]
  }
}
```

In einem Template kann dann zb über `{{ data.widgets.elements[0].numberOfBAInProduction }}` auf die Werte des Widgets zugegriffen werden.
