<?php

class Haanga_Extension_Filter_D3WordCloud{
  public $is_safe = TRUE;
  static function main($obj, $varname){
  	$names = explode(",", $varname);
  	$varList = array();
  	$randId = uniqid("_ID_");
  	$words = array();
    $fieldCounter=0;  
  	foreach($names as $v){
  	  if(strpos($v,"=")){
  	    break;
  	  }
  	  $variable['name'] = $v;
  	  $variable['value'] = 'value';
  	  if(strpos($v, ".")){
  	    $aux = explode(".", $v);
  	    $variable['name'] = $aux[0];
  	    $variable['value'] = $aux[1];
  	  }
  	  $fieldCounter++;
  	  array_push($varList, $variable);
  	}

  	//options
  	$options = array();
  	$options['width'] = 960;
  	$options['height'] = 500;
  	$options['color'] = '#aec7e8';
  	$options['radius'] = 10;
  	for($z=1; $z < count($names); $z++){
      $pair = explode("=", $names[$z]);
      $key = trim($pair[0], "\" '");
      $value = trim($pair[1], "\" '");
      $options[$key] = $value;     
    }

  	
  	foreach($obj as $k){
  	  foreach($varList as $var){
  	    $name = $var['name'];
  	    $val = $var['value'];
  	    $words = array_merge($words, preg_split("/[\s,\.]+/", $k->$name->$val));
   	  }
  	}  	

  	$pre = '<div id="wordcloud'.$randId.'"></div>
<script src="http://d3js.org/d3.v2.min.js?2.9.3"></script>
<script src="https://raw.github.com/jasondavies/d3-cloud/master/d3.layout.cloud.js"></script>
<script>
// Based on http://www.jasondavies.com/wordcloud 
function D3WordCloud'.$randId.'(words, newcfg){
  var cfg = {width: 300,
             height: 300,
             font: "sans-serif",
             color: "black",
             stopwords: ["of", "the", "a", "or", "to", "and", "for", "at", "with", "without", "in", "from", "is", "are", "were", "was", "this", "that", "these", "those", "in", "on"]
  };
  for(i in newcfg){
    cfg[i] = newcfg[i];
  }
  var countingWords = {};
  for(i in words){
    var d = words[i].replace(/[()\.]/gi, "");
    if(cfg.stopwords.indexOf(d)<0){
      if(countingWords[d] != undefined){ 
        countingWords[d] += 1
      }else{
        countingWords[d] = 1
      }
    }
  }
  var totalWords = new Array();
  for(i in countingWords){
    totalWords.push({name: i, total: countingWords[i]});
  }
  d3.layout.cloud().size([cfg.width, cfg.height])
      .words(totalWords.map(function(d) {
              return {text: d.name, size: 10 + 10*(d.total-1)};
      }))
      .rotate(function() { return ~~(Math.random() * 2) * 90; })
      .padding(1)
      .font("arial")
      .fontSize(function(d) { return d.size; })
      .on("end", draw)
      .start();

  function draw(words) {
    d3.select("body").append("svg")
        .attr("width", cfg.width)
        .attr("height", cfg.height)
      .append("g")
        .attr("transform", "translate("+cfg.width/2+","+cfg.height/2+")")
      .selectAll("text")
        .data(words)
      .enter().append("text")
        .style("font-family", cfg.font)
        .style("font-size", function(d) { return d.size + "px"; })
        .attr("text-anchor", "middle")
        .attr("transform", function(d) {
          return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
        })
        .text(function(d) { return d.text; });
  }
}
var words'.$randId.' = '.json_encode($words).';
var options'.$randId.' = '.json_encode($options).'
D3WordCloud'.$randId.'(words'.$randId.', options'.$randId.');
</script>';
  	return $pre;
  }
}
