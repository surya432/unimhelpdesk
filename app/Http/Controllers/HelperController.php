<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemover;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;
use Sastrawi\Dictionary\ArrayDictionary;

trait HelperController
{
    //
    protected $labels = array();
    protected $docs = array();
    protected $tokens = array();
    protected $data = array();
    protected $cocokkan = "/[ ,.?!-:;\\n\\r\\t…_]/u";
    public function tokenize($kata)
    {
        $retval = preg_split($this->cocokkan, mb_strtolower($kata, 'utf8'));
        $retval = array_filter($retval, 'trim');
        $retval = array_values($retval);
        return $retval;
    }
    function kataDasar($words){
        $stemmer = $this->stemp($words);
        return $this->StopWordRemover($stemmer);
    }
    function stemp($words)
    {
        $stemmerFactory = new StemmerFactory();
        $stemmer  = $stemmerFactory->createStemmer();
        $output   = $stemmer->stem($words);
        return $output;
    }
    function StopWordRemover($words)
    {
        $stopWordRemoverFactory = new StopWordRemoverFactory();
        $dictionary = new ArrayDictionary($stopWordRemoverFactory->getStopWords());
        $stopWordRemover = new StopWordRemover($dictionary);
        return $stopWordRemover->remove($words);
    }
    function train($label, $text)
    {
        $tokens = $this->tokenize($this->kataDasar($text));
        if (!isset($this->labels[$label])) {
            $this->labels[$label] = 0;
            $this->data[$label] = [];
            $this->docs[$label] = 0;
        }
        foreach ($tokens as $token) {
            if (!isset($this->tokens[$token])) {
                $this->tokens[$token] = 0;
            }
            if (!isset($this->data[$label][$token])) {
                $this->data[$label][$token] = 0;
            }
            $this->labels[$label]++;
            $this->tokens[$token]++;
            $this->data[$label][$token]++;
        }
        $this->docs[$label]++;
    }
    function classify($text, $tiket_id)
    {
        $totalDocCount = array_sum($this->docs);
        $kataDasar = $this->kataDasar($text);
        $tokens = $this->tokenize($kataDasar);
        $datahasil = array();
        $scores = array();
        $dataHasilHitung = array();
        $datahasil['words'] = $text;
        $datahasil['keysword'] = $kataDasar;
        $datahasil['tiket_id'] = $tiket_id;
        foreach ($this->labels as $label => $labelCount) {
            $logSum = 0;
            $docCount = $this->docs[$label];
            $inversedDocCount = $totalDocCount - $docCount;
            if (0 === $inversedDocCount) {
                continue;
            }
            foreach ($tokens as $token) {
                $totalTokenCount = isset($this->tokens[$token]) ? $this->tokens[$token] : 0;
                if (0 === $totalTokenCount) {
                    continue;
                }
                $tokenCount         = isset($this->data[$label][$token]) ? $this->data[$label][$token] : 0;
                $inversedTokenCount = $this->inversedTokenCount($token, $label);
                $tokenProbabilityPositive = $tokenCount / $docCount;
                $tokenProbabilityNegative = $inversedTokenCount / $inversedDocCount;
                $probability = $tokenProbabilityPositive / ($tokenProbabilityPositive + $tokenProbabilityNegative);
                //rumus konvert to float
                $probability = ((1 * 0.5) + ($totalTokenCount * $probability)) / (1 + $totalTokenCount);
                if (0 === $probability) {
                    $probability = 0.01;
                } elseif (1 === $probability) {
                    $probability = 0.99;
                }
                $logSum += log(1 - $probability) - log($probability);
            }
            $data = 1 / (1 + exp($logSum));
            $datab = array("keys"=> $label, "values" => $data);
            array_push($dataHasilHitung, $datab);
            $scores[$label]= $data;
        }

        arsort($scores, SORT_NUMERIC);
        $sortPrediksi = array_keys($scores, max($scores));
        $datahasil['hasilPrediksi'] = $sortPrediksi[0];
        $datahasil['dataHasil'] = $dataHasilHitung;
        foreach($dataHasilHitung as $b=>$c ){
            //\App\TrainingHasil::create($c);
        }
        return $datahasil;
    }
    function numberlimit($hasilOutputZ)
    {
        return number_format($hasilOutputZ, 5, '.', '');
    }
    function reset()
    {
        $this->labels = array();
        $this->docs = array();
        $this->tokens = array();
        $this->data = array();
    }
    function inversedTokenCount($token, $label)
    {
        $data = $this->data;
        $totalTokenCount = $this->tokens[$token];
        $totalLabelTokenCount = isset($data[$label][$token]) ? $data[$label][$token] : 0;
        $retval = $totalTokenCount - $totalLabelTokenCount;
        return $retval;
    }
    protected function inversedDocCount($label)
    {
        $data = $this->docs;
        unset($data[$label]);
        return array_sum($data);
    }
}
