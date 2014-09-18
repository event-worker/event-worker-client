<?php

require_once('fpdf/fpdf.php');
set_time_limit(0);
define('EURO', chr(128));
date_default_timezone_set('Europe/Helsinki');

/**
 * Class for generating the PDF with FPDF library.
 *
 * Class extending FPDF library for generating PDF from the events.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerFileGenerator extends FPDF
{
    public $today = null;
    public $json = null;

    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        parent::__construct();

        $text = __("Document generated", 'event-worker-translations');
        $this->today = $text . ": " . date("d.m.Y, H:i:s");

        $options = get_option('event_worker_api_endpoint');
        $endpoint = $options['api-endpoint'];

        $url = home_url() . '/' . $endpoint . '/event';
       
        $output = wp_remote_get($url);
        
        $this->json = json_decode($output['body'], true);
        $this->json = $this->json["@graph"];

        $this->SetMargins(20, 0, 20);

        $this->new_page();
    }

    /** 
     * TODO.
     *
     * @param TODO.
     * @param TODO.
     *
     * @return string
     *
     */
    function WordWrap(&$text, $maxwidth)
    {
        $text = trim($text);

        if ($text === '')
        {
            return 0;
        }

        $space = $this->GetStringWidth(' ');
        $lines = explode("\n", $text);
        $text = '';
        $count = 0;

        foreach ($lines as $line)
        {
            $words = preg_split('/ +/', $line);
            $width = 0;

            foreach ($words as $word)
            {
                $wordwidth = $this->GetStringWidth($word);
                if ($wordwidth > $maxwidth)
                {
                    // Word is too long, we cut it.
                    for($i = 0; $i < strlen($word); $i++)
                    {
                        $wordwidth = $this->GetStringWidth(substr($word, $i, 1));
                        if($width + $wordwidth <= $maxwidth)
                        {
                            $width += $wordwidth;
                            $text .= substr($word, $i, 1);
                        }
                        else
                        {
                            $width = $wordwidth;
                            $text = rtrim($text) . "\n" .substr($word, $i, 1);
                            $count++;
                        }
                    }
                }
                elseif ($width + $wordwidth <= $maxwidth)
                {
                    $width += $wordwidth + $space;
                    $text .= $word . ' ';
                }
                else
                {
                    $width = $wordwidth + $space;
                    $text = rtrim($text) . "\n" . $word . ' ';
                    $count++;
                }
            }
            $text = rtrim($text) . "\n";
            $count++;
        }
        $text = rtrim($text);
        $text = utf8_decode($text);
        return $count;
    }

    /** 
     * Get the title.
     *
     * @param int $i the name index
     *
     * @return string
     *
     */
    function get_title($i)
    {
        $title = strtoupper($this->json[$i]['name']);
        return $title;
    }

    /** 
     * Get the date.
     *
     * @param int $i the index
     *
     * @return string
     *
     */
    function get_date($i)
    {
        $d1 = new DateTime($this->json[$i]['startDate']);
        $d2 = new DateTime($this->json[$i]['endDate']);
        return utf8_decode($d1->format('d.m.Y H:i') . ' - ' . $d2->format('d.m.Y H:i'));
    }

    /** 
     * Get the organizer.
     *
     * @param int $i the index
     *
     * @return string
     *
     */
    function get_organizer($i)
    {
        $organizer = $this->json[$i]['organizer']['name'];
        return utf8_decode($organizer);
    }

    /** 
     * Get the organizer address and url.
     *
     * @param int $i the index
     *
     * @return string
     *
     */
    function get_organizer_data_first($i)
    {
        $organizer = $this->json[$i]['organizer']['address'] .
                     " - " . $this->json[$i]['organizer']['url'];

        return utf8_decode($organizer);
    }

        /** 
     * Get the organizer address and url.
     *
     * @param int $i the index
     *
     * @return string
     *
     */
    function get_organizer_data_second($i)
    {
        $organizer = $this->json[$i]['organizer']['telephone'] .
                     " - " . $this->json[$i]['organizer']['email'];

        return utf8_decode($organizer);
    }

    /** 
     * Get the price.
     *
     * @param int $i the index
     *
     * @return string
     *
     */
    function get_price($i)
    {   
        //iconv("UTF-8", "ISO-8859-1", "€");

        $price = $this->json[$i]['offers']['price'];
        return $price;
    }

    /** 
     * Make the PDF document.
     *
     * @param int $i the index
     *
     */
    function make_content($i)
    {   
        $this->SetFont('Times','',10);
        $this->SetTextColor(50, 50, 50);

        $this->SetFillColor(225, 225, 225);

        $this->Cell(80, 4, $this->get_date($i), 0);

        $this->SetTextColor(0, 0, 0);

        $title = $this->get_title($i);

        $this->SetFont('Times','B', 10);
        $this->MultiCell(80, 4, utf8_decode($title), 0);

        $this->SetFont('Times','',10);

        $category_array = $this->json[$i]['keywords'];
        $keywords = implode(', ', $category_array['keywords']);

        $price = ucfirst(__("price", 'event-worker-translations')) . ": " . $this->get_price($i);
       
        $this->cell(80, 4, $price . EURO, 0);
        $this->MultiCell(80, 4,  $keywords, 0);

        //$post = get_page_by_title($title, OBJECT, 'events');

        //$location = get_post_meta($post->ID, 'event_location', true);

        $this->MultiCell(80, 4, utf8_decode($this->json[$i]['location']['name']) . " - " . utf8_decode($this->json[$i]['location']['address']), 0);        


        $this->SetTextColor(50, 50, 50);
        $url = $this->json[$i]['sameAs'];
        $this->WordWrap($url, 160);

        $this->Write(4, $url, $this->json[$i]['sameAs']);

        $this->Ln(5);

        $this->Cell(80, 4, utf8_decode(ucfirst(__("organizer", 'event-worker-translations'))) . ": " . $this->get_organizer($i), 0);
        
        $this->SetTextColor(0, 0, 0);
        $this->Ln();

        $data_temp = $this->get_organizer_data_first($i);
        $this->WordWrap($data_temp, 160);
        $this->Write(4, $data_stemp);

        $this->Cell(80, 4, $this->get_organizer_data_first($i), 0);
        $this->Ln();
        $this->Cell(80, 4, $this->get_organizer_data_second($i), 0);
        $this->Ln(3);
    }

    /** 
     * Make the PDF document.
     *
     * @param int $i the index
     *
     */
    function make_page($i)
    {
        if ($this->getY() <= 217)
        {   
            $this->make_content($i);
        }
        else
        {
            $this->new_page();
            $this->make_content($i);
        }
    }

    /** 
     * A new page.
     *
     */
    function new_page()
    {   
        $this->AddPage();
        $this->SetFont('Times', '', 9);
        $this->Cell(170, 10, $this->today, 0, 0, 'R', false);
        $this->Ln();
    }

    /** 
     * Limit the content words.
     *
     * @param string $string The content string.
     * @param int $limit The word limit.
     *
     * @return string
     *
     */
    function wordlimit($string, $limit)
    {
        $overflow = true;
        $array = explode(' ', $string);

        $output = '';

        for ($i = 0; $i < $limit; $i++)
        {
            if (isset($array[$i]))
            {
                $output .= $array[$i] . ' ';
            }
            else
            {
                $overflow = false;
            }
        }
        return trim($output) . ($overflow ? '...' : '');
    }
}

$generator = new WorkerFileGenerator('P', 'mm', 'A4');

$op = fopen('../events.txt', 'w');
fwrite($op, pack("CCC", 0xef, 0xbb, 0xbf));

fwrite($op, $generator->today);
fwrite($op, "\n" . str_repeat("=", strlen($generator->today)) . "\n\n\n");

for ($i = 0; $i < count($generator->json); $i++)
{
    $generator->make_page($i);
    $content = strip_tags(utf8_decode($generator->json[$i]['description']));
    $temp = $generator->wordlimit($content, 25);

    $generator->Ln(2);
    $generator->SetDrawColor(200, 200, 150);
    $generator->MultiCell(160, 4,  $temp, '', 'L', false);
    $generator->SetTextColor(50, 50, 50);
    $generator->Ln(0.5);
    $generator->Cell(160, 4, __("LINK", 'event-worker-translations') . ': ' . $generator->json[$i]['url'], 'B', 1, 'L', false, $generator->json[$i]['url']);
    $generator->SetTextColor(0,0,0);
    $generator->SetDrawColor(0, 0, 0);
    $generator->Ln(3);
    
    $organizer = ucfirst(__("organizer", 'event-worker-translations'));
    $organizer_address = ucfirst(__("organizer address", 'event-worker-translations'));
    $organizer_phone = ucfirst(__("organizer phone", 'event-worker-translations'));
    $organizer_email = ucfirst(__("organizer e-mail", 'event-worker-translations'));
    $organizer_website = ucfirst(__("organizer website", 'event-worker-translations'));

    fwrite($op, $generator->get_title($i) . "\n" . $generator->get_date($i) . "\n" . ucfirst(__("price", 'event-worker-translations')) . ": " . $generator->get_price($i) . "\xE2\x82\xAc");
    fwrite($op, "\n" . ucfirst(__("website", 'event-worker-translations')) . ": " . $generator->json[$i]['sameAs']);
    fwrite($op, "\n" . ucfirst(__("location", 'event-worker-translations')) . ": " . $generator->json[$i]['location']['name'] . " - " . $generator->json[$i]['location']['address']);
    fwrite($op, "\n" . $organizer . ": " . $generator->json[$i]['organizer']['name']);
    fwrite($op, "\n" . $organizer_address . ": " . $generator->json[$i]['organizer']['address']);
    fwrite($op, "\n" . $organizer_phone . ": " . $generator->json[$i]['organizer']['telephone']);
    fwrite($op, "\n" . $organizer_email . ": " . $generator->json[$i]['organizer']['email']);
    fwrite($op, "\n" . $organizer_website . ": " . $generator->json[$i]['organizer']['url']);
    fwrite($op, "\n\n" . $generator->json[$i]['description']);
    fwrite($op, "\n" . str_repeat("-", strlen($generator->get_date($i))) . "\n\n");
}

fclose($op);
$generator->Output("../events.pdf", "F");

?>