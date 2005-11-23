<?php

function getTime($microtime) {
	list($usec, $sec) = explode(' ', $microtime); 
	return ((float)$usec + (float)$sec); 
}

class Profiler {
	var $stages = array();
	var $tags = array();
	var $currentStage = '';
	var $start;
	var $end;
	
	function Profiler() {
	}
	
	function start() {
		$this->start = microtime();
	}
	
	function end() {
		$this->end = microtime();
		$this->aggregateData();
	}

	function startStage($stage) {
		$this->currentStage .= (empty($this->currentStage) ? '' : '>').$stage;
		$stagePath = $this->currentStage;
		if(!isset($this->stages[$stagePath])) {
			$this->stages[$stagePath] = array();
			$this->stages[$stagePath]['count'] = 0;
			$this->stages[$stagePath]['time'] = array();
		}
		$this->stages[$stagePath]['time'][] = microtime();
	}
	
	function endStage($stage, $tag = false) {
		$this->stages[$this->currentStage]['time'][] = microtime();
		$this->stages[$this->currentStage]['count'] ++;
		
		if($tag) {
			$count = count($this->stages[$this->currentStage]['time']);
			$this->addToTag($tag, $this->stages[$this->currentStage]['time'][$count-2], $this->stages[$this->currentStage]['time'][$count-1]);
		}
		
		$this->currentStage = substr($this->currentStage, 0, strrpos($this->currentStage, '>'));
	}
	
	function addToTag($tag, $start, $end) {
		if(!isset($this->tags[$tag])) {
			$this->tags[$tag] = array();
			$this->tags[$tag]['count'] = 0;
			$this->tags[$tag]['time'] = array();
		}
		$this->tags[$tag]['count'] ++;
		$this->tags[$tag]['time'][] = $start;
		$this->tags[$tag]['time'][] = $end;
	}
	
	function getTags() {
		return $this->tags;
	}
	
	function getCurrentStage() {
		return $this->currentStage;
	}
	
	function getStages() {
		return $this->stages;
	}
	
	function aggregateData() {
		$this->totalTime = getTime($this->end) - getTime($this->start);
		
		foreach($this->stages AS $stageName => $stage) {
			$count = count($stage['time']);
			$totalDuration = 0;
			for($i = 0; $i < $count; $i+=2) {
				$totalDuration += getTime($stage['time'][$i+1]) - getTime($stage['time'][$i]);
			}
			unset($this->stages[$stageName]['time']);
			$this->stages[$stageName]['duration'] = $totalDuration;
		}
		
		foreach($this->tags AS $tagName => $tag) {
			$count = count($tag['time']);
			$totalDuration = 0;
			for($i = 0; $i < $count; $i+=2) {
				$totalDuration += getTime($tag['time'][$i+1]) - getTime($tag['time'][$i]);
			}
			unset($this->tags[$tagName]['time']);
			$this->tags[$tagName]['duration'] = $totalDuration;
		}
	}
	
	function displayProfile() {
		stderr('');
		stderr('###################################################');
		stderr('# Profile                                         #');
		stderr('###################################################');
		stderr('');
		stderr('Total time: '.number_format($this->totalTime, 5));
		
		if(!empty($this->stages)) {
			stderr('');
			stderr('# Stages');
			stderr('');
			foreach($this->stages AS $stagePath => $stage) {
				if(strpos($stagePath, '>') !== false) {
					$stageName = substr($stagePath, strrpos($stagePath, '>') + 1);
				} else {
					$stageName = $stagePath;
				}
				$level = substr_count($stagePath, '>');
				$line = str_repeat('   ', $level);
				$line .= $stageName;
				$line .= ': ';
				$line .= number_format($stage['duration'], 5).' (';
				$line .= 'cnt: '.$stage['count'];
				if($stage['count'] > 1) {
					$line .= ' - avg: '.number_format($stage['duration']/$stage['count'], 5);
				}
				$line .= ' - pct: '.number_format(($stage['duration']/$this->totalTime)*100, 2).'%';
				$line .= ')';
				stderr($line);
			}
		}
		if(!empty($this->tags)) {
			stderr('');
			stderr('# Tags');
			stderr('');
			foreach($this->tags AS $tagName => $tag) {
				$line = $tagName;
				$line .= ': ';
				$line .= number_format($tag['duration'], 5).' (';
				$line .= 'cnt: '.$tag['count'];
				if($tag['count'] > 1) {
					$line .= ' - avg: '.number_format($tag['duration']/$tag['count'], 5);
				}
				$line .= ' - pct: '.number_format(($tag['duration']/$this->totalTime)*100, 2).'%';
				$line .= ')';
				stderr($line);
			}
		}
		stderr('');
	}
}

?>