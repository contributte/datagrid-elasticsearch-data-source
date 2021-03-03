[![Build Status](https://travis-ci.org/ublaboo/datagrid-elasticsearch-data-source.svg?branch=master)](https://travis-ci.org/ublaboo/datagrid-elasticsearch-data-source)
[![Latest Stable Version](https://poser.pugx.org/ublaboo/datagrid-elasticsearch-data-source/v/stable)](https://packagist.org/packages/ublaboo/datagrid-elasticsearch-data-source)
[![License](https://poser.pugx.org/ublaboo/datagrid-elasticsearch-data-source/license)](https://packagist.org/packages/ublaboo/datagrid-elasticsearch-data-source)
[![Total Downloads](https://poser.pugx.org/ublaboo/datagrid-elasticsearch-data-source/downloads)](https://packagist.org/packages/ublaboo/datagrid-elasticsearch-data-source)
[![Gitter](https://img.shields.io/gitter/room/nwjs/nw.js.svg)](https://gitter.im/ublaboo/help)

# ElasticsearchDataSource

There is no problem using Elasticsearch as a data source for `contributte/datagrid`.

## Installation

Download this package using composer:

```
$ composer require ublaboo/datagrid-elasticsearch-data-source
```

## Usage

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
