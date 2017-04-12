<?php
 
//set local datetime for php time
date_default_timezone_set('America/New_York');

/*Conectar la conexion a la base de datos*/
function conectarDB(){ 
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "mwatch";
    
	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname); 
    
	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	//echo "Connected successfully";
        
	//devolvemos el objeto de conexión para usarlo en las consultas  
    return $conn; 
}  

/*Desconectar la conexion a la base de datos*/
function cerrarDB($conn){
	
	//Cierra la conexión y guarda el estado de la operación en una variable
    $close = mysqli_close($conn); 
    
	//Comprobamos si se ha cerrado la conexión correctamente
	if (!$close) {
		die("Close connection failed: " . mysql_error());
	}
	//echo "Disconnected successfully";
    //devuelve el estado del cierre de conexión
    return $close;         
}

//Devuelve un array multidimensional con el resultado de la consulta
function getArraySQL($conexion,$sql){
    
	//generamos la consulta
    if(!$result = mysqli_query($conexion, $sql)) die();
	$rawdata = array();
    
    //guardamos en un array multidimensional todos los datos de la consulta
	$i=0;
    while($row = mysqli_fetch_array($result)) {               
		//guardamos en rawdata todos los vectores/filas que nos devuelve la consulta
        $rawdata[$i] = $row;
        $i++;
    }
    
	//devolvemos rawdata
    return $rawdata;
}

//Devuelve un array multidimensional con el resultado de country list
function getCountries($selected_val, $conexion){
	
	// data for country list
	$qry_country_list = "SELECT country_id, country FROM mwatch.country where country_id < 34 order by country";	
	$res_country_list = getArraySQL($conexion, $qry_country_list);
    
	$options = '<option value="0">Latin America & the Caribbean...</option>';

	for($i=0; $i<count($res_country_list); $i++) { 
		
		if($selected_val == $res_country_list[$i]['country_id']) {
			$options.='<option value="' .$res_country_list[$i]['country_id'] .'" selected >'
			          .$res_country_list[$i]['country'] .'</option>';
		} else {
			$options.='<option value="' .$res_country_list[$i]['country_id'] .'" >'
			          .$res_country_list[$i]['country'] .'</option>';
				}
	}
		
    return $options;
}

//Devuelve un array multidimensional con el resultado de media list
function getMedias($selected_med, $conexion){
	
	// data for media list  
	$qry_media_list = "SELECT media_id, name FROM mwatch.media order by name";	
	$res_media_list = getArraySQL($conexion, $qry_media_list);
	
	$options = '<option value="0">All...</option>';

	for($i=0; $i<count($res_media_list); $i++) { 
		
		if($selected_med == $res_media_list[$i]['media_id']) {
			$options.='<option value="' .$res_media_list[$i]['media_id'] .'" selected >'
			          .$res_media_list[$i]['name'] .'</option>';
		} else {
			$options.='<option value="' .$res_media_list[$i]['media_id'] .'" >'
			          .$res_media_list[$i]['name'] .'</option>';
		}
	}
	
    return $options;
}

//Devuelve un array multidimensional con el resultado de media list
function getTimes($selected_tim){
	
	$res_time_list = array('Today'=>'t2',
						   'This Week'=>'t3',
						   'This Month'=>'t4',
						   'This Year'=>'t5',
						   'Latest News'=>'t6');
	
	$options = '<option value="0">All...</option>';
	while(list($tname, $tval)=each($res_time_list)) {
		if($selected_tim == $tval) {
			$options.='<option value="' .$tval .'" selected >' .$tname .'</option>';
		} else {
			$options.='<option value="' .$tval .'" >' .$tname .'</option>';
		}
	}
	
    return $options;
}

//Devuelve un string con el SELECT statement according to selection criteria
function getSelectSQL($selection, $selected_val, $selected_med, $selected_tim){
	
	$squery = '';
	if($selection == "map") {
		//Bild query for map
		$squery = 'SELECT media_name, lat, lng, city, align, xpoint, ypoint, posit, count(news_id) as total_news FROM mwatch.chart_news WHERE news_id > -1';
		if($selected_val == "0" or $selected_val == "") { $squery .= ''; } else { $squery .= ' AND country_id=' . $selected_val; } 
		if($selected_med == "0" or $selected_med == "") { $squery .= ''; } else { $squery .= ' AND media_id=' . $selected_med; } 
		if($selected_tim == "t2") { $squery .= ' AND pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 24 HOUR ))'; } 
		if($selected_tim == "t3") { $squery .= ' AND pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 7 DAY ))'; } 
		if($selected_tim == "t4") { $squery .= ' AND pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 1 MONTH ))'; } 
		if($selected_tim == "t5") { $squery .= ' AND pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 1 YEAR ))'; } 
		$squery .= ' GROUP BY  media_name,  lat, lng, city, align, xpoint, ypoint, posit ORDER BY city';
				
	} elseif($selection == "pie") {
		//Bild query for pie
		$squery = 'SELECT news_type, count(news_id) as total_news FROM mwatch.chart_news WHERE news_id > -1';
		if($selected_val == "0" or $selected_val == "") { $squery .= ''; } else { $squery .= ' AND country_id=' . $selected_val; } 
		if($selected_med == "0" or $selected_med == "") { $squery .= ''; } else { $squery .= ' AND media_id=' . $selected_med; } 
		if($selected_tim == "t2") { $squery .= ' AND pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 24 HOUR ))'; } 
		if($selected_tim == "t3") { $squery .= ' AND pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 7 DAY ))'; } 
		if($selected_tim == "t4") { $squery .= ' AND pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 1 MONTH ))'; } 
		if($selected_tim == "t5") { $squery .= ' AND pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 1 YEAR ))'; } 
		$squery .= ' GROUP BY  news_type ORDER BY news_type';
		
	} elseif($selection == "bar") {
		//Bild query for bar
		$squery = 'SELECT country, siglas, population_in, (invest_total/1000) as invest, invest_share, count(news_id) as total_news,';
		$squery .='((count(chart_news.news_id))*100)/(SELECT COUNT(n.news_id) FROM NEWS n WHERE n.country_id < 34) AS nws_percent,';	
		$squery .='(population_in*100)/(SELECT sum(c.population_in) FROM COUNTRY c where c.country_id < 34) as pop_percent,';
        $squery .='((invest_total*1000)*100)/(SELECT SUM(c.invest_total*1000) FROM COUNTRY c where c.country_id < 34) as fdi_percent';		
		$squery .= ' FROM mwatch.chart_news WHERE news_id > -1';
		if($selected_val == "0" or $selected_val == "") { $squery .= ''; } else { $squery .= ' AND country_id=' . $selected_val; } 
		if($selected_med == "0" or $selected_med == "") { $squery .= ''; } else { $squery .= ' AND media_id=' . $selected_med; } 
		if($selected_tim == "t2") { $squery .= ' AND pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 24 HOUR ))'; } 
		if($selected_tim == "t3") { $squery .= ' AND pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 7 DAY ))'; } 
		if($selected_tim == "t4") { $squery .= ' AND pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 1 MONTH ))'; } 
		if($selected_tim == "t5") { $squery .= ' AND pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 1 YEAR ))'; } 
		$squery .= ' GROUP BY country ORDER BY country';
		
	} elseif($selection == "real" or $selection == "time") {
		//Bild query for real time chart option t1 for NOW was deleted
					
		if($selected_tim == "t2") {
			$squery = 'SELECT QRY_t2 as p_date, count(news_id) as total_news FROM mwatch.chart_times';
			$squery .= ' WHERE pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 24 HOUR ))'; 
			if($selected_val == "0" or $selected_val == "") { $squery .= ''; } else { $squery .= ' AND country_id=' . $selected_val; } 
			if($selected_med == "0" or $selected_med == "") { $squery .= ''; } else { $squery .= ' AND media_id=' . $selected_med; } 
			
			if($selection == "time") {
				$squery .= ' GROUP BY QRY_t2 ORDER BY DATE_FORMAT(pub_date, "%Y-%m-%d %H:%i:%s") DESC LIMIT 0,1'; }
			else { 	
				$squery .= ' GROUP BY QRY_t2 ORDER BY DATE_FORMAT(pub_date, "%Y-%m-%d %H:%i:%s") ASC'; } }
		
		elseif($selected_tim == "t3") {
			$squery = 'SELECT QRY_t3 as p_date, count(news_id) as total_news FROM mwatch.chart_times';
			$squery .= ' WHERE pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 7 DAY ))'; 
			if($selected_val == "0" or $selected_val == "") { $squery .= ''; } else { $squery .= ' AND country_id=' . $selected_val; } 
			if($selected_med == "0" or $selected_med == "") { $squery .= ''; } else { $squery .= ' AND media_id=' . $selected_med; } 
			$squery .= ' GROUP BY QRY_t3 ORDER BY DATE_FORMAT(pub_date, "%Y-%m-%d") ASC'; } 
		
		elseif($selected_tim == "t4") {
			$squery = 'SELECT QRY_t4 as p_date, count(news_id) as total_news FROM mwatch.chart_times';
			$squery .= ' WHERE pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 1 MONTH ))'; 
			if($selected_val == "0" or $selected_val == "") { $squery .= ''; } else { $squery .= ' AND country_id=' . $selected_val; } 
			if($selected_med == "0" or $selected_med == "") { $squery .= ''; } else { $squery .= ' AND media_id=' . $selected_med; } 
			$squery .= ' GROUP BY QRY_t4 ORDER BY DATE_FORMAT(pub_date, "%Y-%m-%d") ASC'; } 
		
		elseif($selected_tim == "t5") {
			$squery = 'SELECT QRY_t5 as p_date, count(news_id) as total_news FROM mwatch.chart_times';
			$squery .= ' WHERE pub_date >= DATE(DATE_SUB(NOW(), INTERVAL 1 YEAR ))'; 
			if($selected_val == "0" or $selected_val == "") { $squery .= ''; } else { $squery .= ' AND country_id=' . $selected_val; } 
			if($selected_med == "0" or $selected_med == "") { $squery .= ''; } else { $squery .= ' AND media_id=' . $selected_med; } 
			$squery .= ' GROUP BY QRY_t5 ORDER BY DATE_FORMAT(pub_date, "%Y-%m") ASC'; } 
		
		elseif($selected_tim == "t6") {
			$squery = 'SELECT * FROM mwatch.table_news WHERE news_id > -1';
			if($selected_val == "0" or $selected_val == "") { $squery .= ''; } else { $squery .= ' AND country_id=' . $selected_val; } 
			if($selected_med == "0" or $selected_med == "") { $squery .= ''; } else { $squery .= ' AND media_id=' . $selected_med; } 
			$squery .= ' ORDER BY ndate DESC LIMIT 10'; } 
			
		else { 
			$squery = 'SELECT QRY_t0 as p_date, count(news_id) as total_news FROM mwatch.chart_times ';
			$squery .= ' WHERE news_id > -1'; 
			if($selected_val == "0" or $selected_val == "") { $squery .= ''; } else { $squery .= ' AND country_id=' . $selected_val; } 
			if($selected_med == "0" or $selected_med == "") { $squery .= ''; } else { $squery .= ' AND media_id=' . $selected_med; } 
			$squery .= ' GROUP BY QRY_t0 ORDER BY DATE_FORMAT(pub_date, "%Y") ASC'; }  	
			
	} else {
		$squery = 'ERROR'; }
	
	return $squery;
}

	//Read Selected Country Value
	$selected_val = "";
	if(isset($_POST['mycountry'])) 
		$selected_val = $_POST['mycountry'];  // Storing Selected Value In Variable
	
	//Read Selected Media Value
	$selected_med = "";
	if(isset($_POST['mymedia'])) 
		$selected_med = $_POST['mymedia'];  // Storing Selected Value In Variable
	
	//Read Selected Timeframe Value
	$selected_tim = "";
	if(isset($_POST['mytime'])) 
		$selected_tim = $_POST['mytime'];  // Storing Selected Value In Variable
	
	//creamos la conexión
    $conexion = conectarDB();
	
	// allow characters with accents
	mysqli_set_charset( $conexion, 'utf8');
	
	// data for map
	$qry_news_by_media = getSelectSQL("map", $selected_val, $selected_med, $selected_tim);
	$res_news_by_media = getArraySQL($conexion, $qry_news_by_media);
	
	// data for pie chart
	$qry_news_by_type = getSelectSQL("pie", $selected_val, $selected_med, $selected_tim);	
	$res_news_by_type = getArraySQL($conexion, $qry_news_by_type);
	
	// data for bar chart
	$qry_news_by_country = getSelectSQL("bar", $selected_val, $selected_med, $selected_tim);	
	$res_news_by_country = getArraySQL($conexion, $qry_news_by_country);

	// data for real time area
	$qry_news_by_time = getSelectSQL("real", $selected_val, $selected_med, $selected_tim);	
	$res_news_by_time = getArraySQL($conexion, $qry_news_by_time);
	
	// data for news table
	//$qry_news_table = getSelectSQL("real", $selected_val, $selected_med, $selected_tim);	
	//$res_news_table = getArraySQL($conexion, $qry_news_table);
	
	//cerramos la base de datos
	//cerrarDB($conexion);
	//unset($conexion);
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Media Watch - Canada</title>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="http://code.highcharts.com/highcharts.js"></script>
	<script src="http://code.highcharts.com/highcharts-more.js"></script>
	<script src="http://code.highcharts.com/highcharts-3d.js"></script>
    <script src="http://code.highcharts.com/maps/modules/map.js"></script>

    <style type="text/css">
        #container {
            height: 680px;
            min-width: 310px;
            max-width: 800px;
            margin: 0 auto;
        }

        .loading {
            margin-top: 10em;
            text-align: center;
            color: gray;
        }
    </style>

    <script type="text/javascript">

        // Initiate the chart
        $(function () {
            // Map for Total News by City and Media
            $('#container').highcharts('Map', {
                title: {
                    text: ''
                },
                credits: {
                    enabled: false
                },
                exporting: {
                    enabled: false
                },
                mapNavigation: {
                    enabled: false

                },
                tooltip: {
                    headerFormat: '',
                    pointFormat:
                    '<b>{point.name}<b><br>' +
                    'City:<b>{point.city}<b><br>' +
                    'Total News: <b>{point.news}<b>'
                },
                series: [{
                    // Use the gb-all map with no data as a basemap
                    mapData: Highcharts.maps['countries/ca/ca-all'],
                    name: 'Basemap',
                    borderColor: '#707070', // #fb0000
                    nullColor: '#fb9595',
                    showInLegend: false
                }, {
                    name: 'Separators',
                    type: 'mapline',
                    data: Highcharts.geojson(Highcharts.maps['countries/ca/ca-all'], 'mapline'),
                    color: '#707070',
                    showInLegend: false,
                    enableMouseTracking: false
                }, {
                    // Specify points using lat/lon
                    type: 'mappoint',
                    name: '',
                    showInLegend: false,
                    color: Highcharts.getOptions().colors[1],
                    data: [
					<?php
						// data for map
						for($i=0; $i<count($res_news_by_media); $i++) {
					?>
					{
                        name: '<?php echo $res_news_by_media[$i]['media_name'] ?>',
                        lat: <?php echo $res_news_by_media[$i]['lat'] ?>,
                        lon: <?php echo $res_news_by_media[$i]['lng'] ?>,
                        city: '<?php echo $res_news_by_media[$i]['city'] ?>',
                        news: <?php echo $res_news_by_media[$i]['total_news'] ?>,
						dataLabels: {
                            align: '<?php echo $res_news_by_media[$i]['align'] ?>',
                            x: <?php echo $res_news_by_media[$i]['xpoint'] ?>,
							y: <?php echo $res_news_by_media[$i]['ypoint'] ?>,
                            verticalAlign: '<?php echo $res_news_by_media[$i]['posit'] ?>'
                        }
                    }, 
					<?php }	?>
					]
                }]
            });

            // Pie Chart News by Type
            $('#pie').highcharts({
                chart: {
                    type: 'pie',
                    borderWidth: 0
                },
                title: {
                    text: '',
                },
                subtitle: {
                    text: '',
                },
                credits: {
                    enabled: false
                },
                exporting: {
                    enabled: false
                },
                tooltip: {
					headerFormat: '',
                    pointFormat: '{point.name}: <b> {point.y} ( {point.percentage:.1f}% )</b>'
                },
                plotOptions: {
                    pie: {
                        dataLabels: {
                            enabled: true,

                            // format: '{point.name}<br/> {point.percentage:.1f}% ',
                            distance: -50,
                            style: {
                                fontWeight: 'bold',
                                color: 'black',
                                width: '100px'
                            }

                        }
                    }
                },
                series: [{
                    startAngle: 90,
                    name: '',
                    data: [
					<?php for($i=0; $i<count($res_news_by_type); $i++) { ?>
						['<?php echo $res_news_by_type[$i]['news_type'] ?>', <?php echo $res_news_by_type[$i]['total_news'] ?> ], 
					<?php } ?>
                    ]
                }]
            });

            // Bar Chart for Total News by Country
            $('#bars').highcharts({
                chart: {
                    type: 'column',
					marginLeft: 0,
					spacingLeft: 0,
					options3d: {
						enabled: true,
						alpha: 10,
						beta: 25,
						depth: 70
					},
                    borderWidth: 0,
					showAxes: true
                },
                title: {
                    text: '',
                },
                subtitle: {
                    text: '',
                },
				plotOptions: {
					column: {
						depth: 25
					}
				},
                credits: {
                    enabled: false
                },
                exporting: {
                    enabled: false
                },
				legend: {
					layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'top',
                    backgroundColor: '#fff',
                    borderColor: '#ccc',
                    borderWidth: .5,
                    y: 0,
                    x: 0,
                    itemWidth: 180,
                    itemStyle: {
						fontWeight: 'bold',
                        fontSize: '9px'
                    },
                    itemHiddenStyle: {
						fontWeight: 'bold',
                        fontSize: '9px'
                    }
                },				
				tooltip: {
					headerFormat: '<span style="font-size:12px"><b>{point.key}</b></span><table>',
					pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
								'<td style="padding:0"><b>{point.y}</b></td></tr>',
					footerFormat: '</table>',
					shared: true,
					useHTML: true	
				},
				xAxis: {
					lineColor: '#999',
                    lineWidth: 1,
                    tickColor: '#666',
                    tickLength: 3,
                    title: {
						style: {
							color: '#333'
                        }
                    },	
					categories: [
					//data for bar chart
					<?php for($i=0; $i<count($res_news_by_country); $i++) { ?>
						['<?php echo $res_news_by_country[$i]['country'] ?>'], 
					<?php } ?>
					]
				},
                yAxis: {
					lineColor: '#999',
                    lineWidth: 1,
                    tickColor: '#666',
                    tickWidth: 1,
                    tickLength: 3,
					<?php if($selected_val == "0" or $selected_val == "") { ?>
						gridLineColor: '#ddd',				
					<?php } else { ?>
						gridLineColor: '#ddd',
						//max: 500,
					<?php }	?>
					title: {
						text: '',
                        rotation: 0,
                        margin: 20,
                        style: {
							color: '#333'
                        }
                    },
                    labels: {
						style: {
							fontSize: '10px',
                            color: '#333',
                        },
                        margin: 10
                    },
                },	
                series: [{
					<?php if($selected_val == "0" or $selected_val == "") { ?>
                    name: 'Total News',
                    showInLegend: false,
					color: '#7cb5ec',
                    data: [
					// data for bar chart
					<?php for($i=0; $i<count($res_news_by_country); $i++) { ?>
						[<?php echo $res_news_by_country[$i]['total_news'] ?>],
					<?php }	?> 
					]
					<?php } else { ?>
					name: 'Total News (Regional Share)',
					showInLegend: false,
					color: '#7cb5ec',
					data: [  
					<?php for($i=0; $i<count($res_news_by_country); $i++) {	?>
					[ <?php echo  $res_news_by_country[$i]['nws_percent'] ?> ],
					<?php } ?> 
					]
					, tooltip: {
						valueSuffix: '%',
						valueDecimals: 2
					},
					}, {
					name: 'Population in Canada (Regional Share)',
					showInLegend: false,
					color: '#ab2121',
					data: [  
					<?php for($i=0; $i<count($res_news_by_country); $i++) {	?>
					[ <?php echo  $res_news_by_country[$i]['pop_percent'] ?> ],
					<?php } ?> 
					]
					, tooltip: {
						valueSuffix: '%',
						valueDecimals: 2
					},
					}, {
					name: 'Canadian Direct Investment (Regional Share)',
					showInLegend: false,
					color: '#f7a35c', 
					data: [
					<?php for($i=0; $i<count($res_news_by_country); $i++) {	?>
					[<?php echo $res_news_by_country[$i]['fdi_percent'] ?>],
					<?php } ?> 
					]
					, tooltip: {
						valueSuffix: '%',
						valueDecimals: 2
					},
					}, {
					name: 'Canadian Direct Investment (Global Share)',
					showInLegend: false,
					color: '#8085e9',
					data: [
					<?php for($i=0; $i<count($res_news_by_country); $i++) {	?>
					[<?php echo $res_news_by_country[$i]['invest_share'] ?>],
					<?php } ?> 
					]
					, tooltip: {
						valueSuffix: '%',
						valueDecimals: 2
					}
				<?php }	?>
                }]
            });
			
			<?php if($selected_tim == "t6") { ?>
			//************************************
			//************************************
			//*** Bubble Chart for Latest News ***
			//************************************
			//************************************
			Highcharts.chart('tiempoReal', {
				chart: {
					type: 'bubble',
					plotBorderWidth: 1,
					zoomType: 'xy'
				},
				legend: {
					enabled: false
				},
				title: {
					text: ''
				},
				subtitle: {
					text: ''
				},
                credits: {
                    enabled: false
                },
                exporting: {
                    enabled: false
                },
				xAxis: {
					//tickInterval: (24 * 3600 * 1000),
					//type: 'datetime',
					gridLineWidth: 1,
					title: {
						text: ''
					},
					labels: {
						formatter: function() {return Highcharts.dateFormat('%d-%b-%Y', this.value); } 	
					},
					//plotLines: [{
					//	color: 'black',
					//	dashStyle: 'dot',
					//	width: 2,
					//	value: 10,
					//	label: {
					//		rotation: 0,
					//		y: 15,
					//		style: {
					//			fontStyle: 'italic'
					//		},
					//		text: 'Most News published this period'
					//	},
					//	zIndex: 3
					//}]
				},
				yAxis: {
					startOnTick: false,
					endOnTick: false,
					title: {
						text: 'Latest News'
					},
					labels: {
						format: ''
					},
					maxPadding: 0.2,
					max : 120,
					visible: false
					//plotLines: [{
					//	color: 'black',
					//	dashStyle: 'dot',
					//	width: 2,
					//	value: 10,
					//	label: {
					//		align: 'right',
					//		style: {
					//			fontStyle: 'italic'
					//		},
					//		text: 'Average News for this period',
					//		x: -10
					//	},
					//	zIndex: 3
					//}]
				},
				tooltip: {
					backgroundColor: 'none',
					borderWidth: 0,
					shadow: false,
					useHTML: true,
					padding: 0,
					headerFormat: '<table>',
					pointFormat: '<span class="f32"><span class="flag {point.siglas}"></span></span><b>{point.country}:</b>' +
						'<tr><th>Title: </th><td>{point.name}</td></tr>' +
						//'<tr><th>Title: </th><td><a href="{point.nurl}">{point.name}</a></td></tr>' +
						'<tr><th>Media: </th><td>{point.media}</td></tr>' +
						'<tr><th>Pulished: </th><td>{point.x:%d-%b-%Y}</td></tr>' +
						'<tr><th>Author: </th><td>{point.author} [{point.source}]</td></tr>',
					footerFormat: '</table>',
					followPointer: true,
					positioner: function () { 
						return { x: 20, y: 20 };
					}
				},
				plotOptions: {
					series: {
						cursor:'pointer',
						point:{
							events:{
								click:function() {
									var href = this.nurl;
									window.open(href);
								}
							}
						},
						dataLabels: {
							enabled: true,
							useHTML: true,
							format: '{point.name}'
						}
					}
				},
				series: [{
					data: [
						<?php
						//data for latest news
						for($i=0; $i<count($res_news_by_time); $i++) {	?>
						{ x: <?php echo $res_news_by_time[$i]['p_date'] ?>,  
						  y: <?php echo 100 - ($i * 20) ?>, 
						  z: 50, 
						  name: "<?php echo $res_news_by_time[$i]['title'] ?>",
						  country: "<?php echo $res_news_by_time[$i]['country'] ?>",
						  siglas: "<?php echo $res_news_by_time[$i]['siglas'] ?>",
						  media: "<?php echo $res_news_by_time[$i]['name'] ?>",
						  author: "<?php echo $res_news_by_time[$i]['author_name'] ?>",
						  source: "<?php echo $res_news_by_time[$i]['source'] ?>",
						  nurl: "<?php echo $res_news_by_time[$i]['nurl'] ?>"
						},
						<?php } ?>
					]
				}]

			});
			<?php } else { ?>
			//*********************************
			//*********************************
			//*** Real Time Spline for News ***
			//*********************************
			//*********************************
            $(document).ready(function() {
				//define new last values in DB for date=x and news=y
				var ultimo_x;
				var ultimo_y;	
				//get last DB values - If Null x=now y=0
				var last_db_x = null,
					last_db_y = null;
				<?php if(empty($res_news_by_time[(count($res_news_by_time) - 1)])) { ?>
					//last_db_x = Date.UTC((new Date()).getFullYear(), (new Date()).getMonth(), (new Date()).getDate(), (new Date()).getHours(), (new Date()).getMinutes(), (new Date()).getSeconds()),
					last_db_x = (new Date()).getTime();
					last_db_y = 0;	
				<?php } else { ?>
					last_db_x = <?php echo $res_news_by_time[(count($res_news_by_time) - 1)]['p_date'] ?> ;
					last_db_y = <?php echo $res_news_by_time[(count($res_news_by_time) - 1)]['total_news'] ?> ;
				<?php } ?>
				
				//setting last values in db as new values for first time
				setx(last_db_x);  		
				sety(last_db_y);		
                var chart;
                $('#tiempoReal').highcharts({
                    chart: {
                        type: 'area',
                        animation: Highcharts.svg,
                        marginRight: 10,
                        events: {
                            load: function() { series = this.series[0]; }
                        }
                    },
                    title: {
                        text: ''
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        type: 'datetime',
                        tickPixelInterval: 150,
						<?php if($selected_tim == "t2") { ?>
						labels: { formatter: function() {return Highcharts.dateFormat('%H:%M:%S', this.value); } }
						<?php } elseif($selected_tim == "t3") { ?>
						labels: { formatter: function() {return Highcharts.dateFormat('%A, %b %e, %Y', this.value); } }
						<?php } ?>
                    },
					plotOptions: {
						series: {
							marker: {
								enabled: true
							}
						}
					},
                    yAxis: {
                        title: {
							<?php if($selected_tim == "t2") { ?>
							text: "Today's News" 
							<?php } elseif($selected_tim == "t3") { ?>
							text: "This Week's News"
							<?php } elseif($selected_tim == "t4") { ?>
							text: "This Month's News"
							<?php } elseif($selected_tim == "t5") { ?>
							text: "This Year's News"
							<?php } else { ?>
							text: "All News"
							<?php } ?>
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#AB2121'
                        }]
                    },
                    tooltip: {
                        formatter: function() {
						<?php if($selected_tim == "t2") { ?>
                            return '<b>'+ Highcharts.dateFormat('%b %e, %Y @ %H:%M:%S', this.x) +'</b><br/>'+
									this.series.name + ': ' +'<b>'+ Highcharts.numberFormat(this.y, 0)+'</b><br/>';
						<?php } elseif($selected_tim == "t3") { ?>
                            return '<b>'+ Highcharts.dateFormat('%a, %b %e, %Y', this.x) +'</b><br/>'+
									this.series.name + ': ' +'<b>'+ Highcharts.numberFormat(this.y, 0)+'</b><br/>';
						<?php } elseif($selected_tim == "t4") { ?>
                            return '<b>'+ Highcharts.dateFormat('%b %e, %Y', this.x) +'</b><br/>'+
									this.series.name + ': ' +'<b>'+ Highcharts.numberFormat(this.y, 0)+'</b><br/>';
						<?php } elseif($selected_tim == "t5") { ?>
                            return '<b>'+ Highcharts.dateFormat('%B, %Y', this.x) +'</b><br/>'+
									this.series.name + ': ' +'<b>'+ Highcharts.numberFormat(this.y, 0)+'</b><br/>';
						<?php } else { ?>
                            return 'Year: '+'<b>'+Highcharts.dateFormat('%Y', this.x) +'</b><br/>'+
									this.series.name + ': ' +'<b>'+ Highcharts.numberFormat(this.y, 0)+'</b><br/>';
						<?php } ?>			
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    credits: {
                        enabled: false
                    },
                    exporting: {
                        enabled: false
                    },
                    series: [{
                        name: 'Total News',
						data: [  
						<?php if(empty($res_news_by_time[(count($res_news_by_time) - 1)])) { ?> 
						[<?php echo "Date.UTC(" . date("Y, m-1, d, H, i, s") . ")" ?>, <?php echo "0" ?>],
						<?php } else { ?>
						
						<?php for($i=0; $i<count($res_news_by_time); $i++) { ?>
						[<?php echo $res_news_by_time[$i]['p_date'] ?>, <?php echo $res_news_by_time[$i]['total_news'] ?>],
						<?php } } ?> 
						]
                    }]
                });
								
				<?php if($selected_tim == "t2") { ?>
				//setting interval for todays news
				var new_x = null,
					new_y = null,	
					hoy_x = null,						
					hoy_y = null;
		
				setInterval(function () {

					<?php 
					$qry_new_time = getSelectSQL("time", $selected_val, $selected_med, $selected_tim);	
					$res_new_time = getArraySQL($conexion, $qry_new_time);
					?>
					
					//getting last new values from db
					<?php if(empty($res_new_time[(count($res_new_time) - 1)])) { ?>
						new_x = Date.UTC((new Date()).getFullYear(), (new Date()).getMonth(), (new Date()).getDate(), (new Date()).getHours(), (new Date()).getMinutes(), (new Date()).getSeconds());
						new_y = 0;	
					<?php } else { ?>
						new_x = <?php echo $res_new_time[(count($res_new_time) - 1)]['p_date']  ?> ;
						new_y = <?php echo $res_new_time[(count($res_new_time) - 1)]['total_news'] ?> ;
					<?php } ?>
			
					//getting todays values when new and old are equal
						hoy_x = Date.UTC((new Date()).getFullYear(), (new Date()).getMonth(), (new Date()).getDate(), (new Date()).getHours(), (new Date()).getMinutes(), (new Date()).getSeconds());				
						hoy_y = gety();
					if (new_x == getx()) {
						
                        series.addPoint([hoy_x, hoy_y], series.data.length < 99);
						//chart.redraw();
						//setx(hoy_x);
						//sety(hoy_y);       
						
					} else {
						//alert("NOT-EQUAL ==> new_X= " +  new_x + " / new_Y= " +  new_y );	
						//Draw new point when last_point_drawed = last_point_in_db
						series.addPoint([new_x, new_y], series.data.length < 99);
						
						setx(new_x);
						sety(new_y);  
					} 
                    
				}, 5000);
				
				<?php } ?>
				
				function getx() {return ultimo_x;}
				function gety() {return ultimo_y;}
				function setx(x) {ultimo_x = x;}
				function sety(y) {ultimo_y = y;}
							
            });
            <?php }	?>
        });

    </script>

</head>
<body>

    <!-- Add the Chart Options to a selection dropdown menu -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" style="display: inline-block;">
        Select a Country:
        <select id="mycountry" name="mycountry" type="text" style="font-weight: bold">
			<?php echo getCountries($selected_val, $conexion); ?>
        </select>
		... News Media:
        <select id="mymedia" name="mymedia" type="text" style="font-weight: bold">
			<?php echo getMedias($selected_med, $conexion); ?>
        </select>
		... Timeframe:
        <select id="mytime" name="mytime" type="text" style="font-weight: bold">
            <?php echo getTimes($selected_tim); ?>
        </select>
		
		<!-- Add the Submit Button -->
		<button type="button" style="display: inline-block; margin-left:10px" onclick="this.form.submit()">Submit Selection</button>
    
	</form>
	
    <!-- Add the Button to See Latest News -->
    <!-- <button type="button" style="display: inline-block; margin-left:10px; float: right" onclick="SeeBar()">See Bar Chart</button> -->

    <!-- Add the Button to See list of news according to criteria -->
    <!-- <button type="button" style="display: inline-block;float: right" onclick="ListNews()">List News</button> -->


    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.3.6/proj4.js"></script>
    <script src="https://code.highcharts.com/maps/highmaps.js"></script>
    <script src="https://code.highcharts.com/maps/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/mapdata/countries/ca/ca-all.js"></script>

	<!-- Flag sprites service provided by Martijn Lafeber, https://github.com/lafeber/world-flags-sprite/blob/master/LICENSE -->
	<link rel="stylesheet" type="text/css" href="//cloud.github.com/downloads/lafeber/world-flags-sprite/flags32.css"/>

    <!-- Add border line -->
    <div style="border-top:1px solid #CDCDCD;margin:10px;padding:0;clear:both;"></div>

    <!-- Add container for map -->
    <div id="container" style="width: 34%; height: 350px; margin: 0 auto;float:left;"></div>

    <!-- Add container for pie -->
    <div id="pie" style="width: 18%; height: 350px; margin: 0 auto;float:left;"></div>

    <!-- Add container for bar chart -->
    <div id="bars" style="width: 48%; height: 350px; margin: 0 auto; float:left;"></div>

    <!-- Add border line -->
    <div style="border-top:1px solid #CDCDCD;margin:10px;padding:0;clear:both;"></div>

    <!-- Add real time botton graph -->
    <div id="tiempoReal" style="width: 100%; height: 320px; margin: 0 auto;float:left;"></div>

</body>
</html>