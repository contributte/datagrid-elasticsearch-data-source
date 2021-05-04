![](https://heatbadger.now.sh/github/readme/contributte/datagrid-elasticsearch-data-source/?deprecated=1)

<p align=center>
    <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
    <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
    <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
    Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

## Disclaimer

| :warning: | This project is no longer being maintained. Please use [ublaboo/datagrid](https://github.com/ublaboo/datagrid).
|---|---|

| Composer | [`ublaboo/datagrid-elasticsearch-data-source`](https://packagist.org/packages/ublaboo/datagrid-elasticsearch-data-source) |
|---| --- |
| Version | ![](https://badgen.net/packagist/v/ublaboo/datagrid-elasticsearch-data-source) |
| PHP | ![](https://badgen.net/packagist/php/ublaboo/datagrid-elasticsearch-data-source) |
| License | ![](https://badgen.net/github/license/ublaboo/datagrid-elasticsearch-data-source) |

## Documentation

### Usage

```php
<?php

namespace App\Presenters;

use Elasticsearch\Client;
use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DatagridElasticsearchDataSource\ElasticsearchDataSource;

final class UsersPresenter extends Presenter
{

	/**
	 * @var ElasticsearchDataSource
	 */
	private $elasticsearchDataSource;


	public function __construct(Client $client)
	{
		$this->elasticsearchDataSource = new ElasticsearchDataSource(
			$client, // Elasticsearch\Client
			'users', // Index name
			'user' // Index type
		);
	}


	public function createComponentUsersGrid(): DataGrid
	{
		$grid = new DataGrid;

		$grid->setDataSource($this->elasticsearchDataSource);

		$grid->addColumnText('id', '#')->setSortable();
		$grid->addColumnLink('nickname', 'Nickname', 'edit')
			->setFilterText();
		$grid->addColumnText('username', 'E-mail (username)')
			->setFilterText();
		$grid->addColumnText('age', 'Age')
			->setSortable()
			->setFilterRange();
		$grid->addColumnText('status', 'Status')
			->setFilterMultiSelect([
				'active' => 'Active',
				'disabled' => 'Disabled',
			]);
		$grid->addColumnDateTime('created', 'Created')
			->setFormat('j. n. Y H:i:s')
			->setFilterDateRange();

		return $grid;
	}
}
```

## Development

This package was maintain by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

<a href="https://github.com/paveljanda">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/1488874?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for being used this package.
