<?php

declare(strict_types=1);

/**
 * @copyright   Copyright (c) 2015 ublaboo <ublaboo@paveljanda.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     Ublaboo
 */

namespace Ublaboo\DatagridElasticsearchDataSource;

use Elasticsearch\Client;
use Ublaboo\DataGrid\DataSource\FilterableDataSource;
use Ublaboo\DataGrid\DataSource\IDataSource;
use Ublaboo\DataGrid\Filter;
use Ublaboo\DataGrid\Utils\DateTimeHelper;
use Ublaboo\DataGrid\Utils\Sorting;

final class ElasticsearchDataSource extends FilterableDataSource implements IDataSource
{

	/**
	 * @var Client
	 */
	private $client;

	/**
	 * @var SearchParamsBuilder
	 */
	private $searchParamsBuilder;

	/**
	 * @var callable
	 */
	private $rowFactory;


	public function __construct(Client $client, string $indexName, string $indexType, ?callable $rowFactory = NULL)
	{
		$this->client = $client;
		$this->searchParamsBuilder = new SearchParamsBuilder($indexName, $indexType);

		if ( ! $rowFactory) {
			$rowFactory = static function (array $hit): array {
				return $hit['_source'];
			};
		}
		$this->rowFactory = $rowFactory;
	}


	public function getCount(): int
	{
		$searchResult = $this->client->search($this->searchParamsBuilder->buildParams());

		return is_array($searchResult['hits']['total']) ? $searchResult['hits']['total']['value'] : $searchResult['hits']['total'];
	}


	public function getData(): array
	{
		if (empty($searchResult)) {
			$searchResult = $this->client->search($this->searchParamsBuilder->buildParams());
		}

		return array_map(
			$this->rowFactory,
			$searchResult['hits']['hits']
		);
	}


	public function filterOne(array $condition): self
	{
		foreach ($filter->getCondition() as $column => $value) {
			$this->searchParamsBuilder->addIdsQuery($value);
		}

		return $this;
	}


	public function applyFilterDate(Filter\FilterDate $filter): self
	{
		foreach ($filter->getCondition() as $column => $value) {
			$timestampFrom = null;
			$timestampTo = null;

			if ($value) {
				$dateFrom = DateTimeHelper::tryConvertToDateTime($value, [$filter->getPhpFormat()]);
				$dateFrom->setTime(0, 0, 0);

				$timestampFrom = $dateFrom->getTimestamp();

				$dateTo = DateTimeHelper::tryConvertToDateTime($value, [$filter->getPhpFormat()]);
				$dateTo->setTime(23, 59, 59);

				$timestampTo = $dateTo->getTimestamp();

				if ($timestampFrom || $timestampTo) {
					$this->searchParamsBuilder->addRangeQuery($column, $timestampFrom, $timestampTo);
				}
			}
		}

		return $this;
	}


	public function applyFilterDateRange(Filter\FilterDateRange $filter): self
	{
		foreach ($filter->getCondition() as $column => $values) {
			$timestampFrom = null;
			$timestampTo = null;

			if ($values['from']) {
				$dateFrom = DateTimeHelper::tryConvertToDateTime($values['from'], [$filter->getPhpFormat()]);
				$dateFrom->setTime(0, 0, 0);

				$timestampFrom = $dateFrom->getTimestamp();
			}

			if ($values['to']) {
				$dateTo = DateTimeHelper::tryConvertToDateTime($values['to'], [$filter->getPhpFormat()]);
				$dateTo->setTime(23, 59, 59);

				$timestampTo = $dateTo->getTimestamp();
			}

			if ($timestampFrom || $timestampTo) {
				$this->searchParamsBuilder->addRangeQuery($column, $timestampFrom, $timestampTo);
			}
		}

		return $this;
	}


	public function applyFilterRange(Filter\FilterRange $filter): self
	{
		foreach ($filter->getCondition() as $column => $value) {
			$this->searchParamsBuilder->addRangeQuery($column, $value['from'] ?: null, $value['to'] ?: null);
		}

		return $this;
	}


	public function applyFilterText(Filter\FilterText $filter): self
	{
		foreach ($filter->getCondition() as $column => $value) {
			if ($filter->isExactSearch()) {
				$this->searchParamsBuilder->addMatchQuery($column, $value);
			} else {
				$this->searchParamsBuilder->addPhrasePrefixQuery($column, $value);
			}
		}

		return $this;
	}


	public function applyFilterMultiSelect(Filter\FilterMultiSelect $filter): self
	{
		foreach ($filter->getCondition() as $column => $values) {
			$this->searchParamsBuilder->addBooleanMatchQuery($column, $values);
		}

		return $this;
	}


	public function applyFilterSelect(Filter\FilterSelect $filter): self
	{
		foreach ($filter->getCondition() as $column => $value) {
			$this->searchParamsBuilder->addMatchQuery($column, $value);
		}

		return $this;
	}


	public function limit($offset, $limit): self
	{
		$this->searchParamsBuilder->setFrom($offset);
		$this->searchParamsBuilder->setSize($limit);

		return $this;
	}


	/**
	 * @throws \RuntimeException
	 */
	public function sort(Sorting $sorting): self
	{
		if (is_callable($sorting->getSortCallback())) {
			throw new \RuntimeException('No can do - not implemented yet');
		}

		foreach ($sorting->getSort() as $column => $order) {
			$this->searchParamsBuilder->setSort(
				[$column => ['order' => strtolower($order)]]
			);
		}

		return $this;
	}
}
