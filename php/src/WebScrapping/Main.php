<?php

namespace Chuva\Php\WebScrapping;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
/**
 * Runner for the Webscrapping exercice.
 */
class Main {

  /**
   * Main runner, instantiates a Scrapper and runs.
   */
  public static function run(): void {
    $dom = new \DOMDocument('1.0', 'utf-8');
    @$dom->loadHTMLFile(__DIR__ . '/../../assets/origin.html');

    $data = (new Scrapper())->scrap($dom);

    // Generate spreadsheet file
    generate_spreadsheet($data);
  }

}

function generate_spreadsheet(Array $data): void {
  // Initializes the spreadsheet writer
  $writer = WriterEntityFactory::createXLSXWriter();
  $writer->openToFile('src/output/spreadsheet.xlsx'); // Output file

  // Spreadsheet headers
  $headers = ['ID', 'Title', 'Type'];

  // Get the maximum number of authors on any Paper
  $maxAuthors = 0;
  foreach ($data as $paper) {
      $maxAuthors = max($maxAuthors, count($paper->authors));
  }

  // Adds author and institution columns based on maximum number of authors
  for ($i = 1; $i <= $maxAuthors; $i++) {
      $headers[] = "Author $i";
      $headers[] = "Author $i Institution";
  }

  // Adds worksheet headers
  $writer->addRow(WriterEntityFactory::createRowFromArray($headers));

  // Add role and actor data to the spreadsheet
  foreach ($data as $paper) {
      $rowData = [
          $paper->id,
          $paper->title,
          $paper->type,
      ];

      foreach ($paper->authors as $author) {
          $rowData[] = $author->name;
          $rowData[] = $author->institution;
      }

      $writer->addRow(WriterEntityFactory::createRowFromArray($rowData));
  }
  $writer->close();
}