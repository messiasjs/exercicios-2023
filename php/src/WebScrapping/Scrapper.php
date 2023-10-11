<?php

namespace Chuva\Php\WebScrapping;

use Chuva\Php\WebScrapping\Entity\Paper;
use Chuva\Php\WebScrapping\Entity\Person;

/**
 * Does the scrapping of a webpage.
 */
class Scrapper {

  /**
   * Loads paper information from the HTML and returns the array with the data.
   */
  public function scrap(\DOMDocument $dom): array {
    $papers = [];
    
    // Finds all elements <a> in DOM
    $elements = $dom->getElementsByTagName('a');

    foreach ($elements as $element) {
      // Checks if the element has the desired class
      if (strpos($element->getAttribute('class'), "paper-card") !== false) {
        
        //Finds element 'a' with class 'paper-card' and get attribute 'href'
        $link = $element->getAttribute('href');
        
        //Encontre o elemento 'h4' com a classe 'my-xs paper-title' e obtenha o seu texto
        $title = $element->getElementsByTagName('h4')->item(0)->textContent;
        
        // Find the 'span' elements within the 'authors' class to get names and institutions
        $authors = [];
        $authorsNodes = $element->getElementsByTagName('div')->item(0)->getElementsByTagName('span');
        foreach ($authorsNodes as $authorNode) {
            $author = $authorNode->textContent;
            $institution = $authorNode->getAttribute('title');
            $authors[] = new Person($author, $institution);
        }
        
        // Find element 'div' with class 'tags mr-sm' and get type
        $type = $element->getElementsByTagName('div')->item(1)->textContent;

        // Find element 'div' with class 'volume-info' and get its text
        $id = $element->getElementsByTagName('div')->item(3)->getElementsByTagName('div')->item(1)->textContent;

        $paper = new Paper($id, $title, $type, $authors);
        $papers[] = $paper;
      }
    }
    return $papers;
  }

}
