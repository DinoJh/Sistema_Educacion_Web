<?php
$head2 = "";
foreach ($head as $h) {
	$name = "name:'" . $h['name'] . "'";
	$format = "formatter:" . $h['formato'];
	$hidden = "hidden:" . $h['hidden'];
	$width = "width:'" . $h['width'] . "'";

	$head2 = $head2 . "{ $name, $width, $format, $hidden, $width },";
}
?>
<div class="card border-2 border-<?php echo $clase; ?>">
	<div class="card-body">
		<div id="<?php echo $id; ?>"></div>
	</div>
</div>

<script>
	dataTable = Array();
	<?php
	$data = (array)$data;

	foreach ($data as $d) {
		$d = (array)$d;
		echo "
	reg=[";
		foreach ($head as $h) {
			echo "'" . $d[$h['campo']] . "',";
		}
		echo "
	];
	dataTable.push(reg);
	";
	}
	?>
	grid = new gridjs.Grid({
		columns: [
			<?php echo $head2; ?>
			<?php foreach ($botones as $b) {
				echo $b;
			} ?>
		],
		//resizable: true,
		sort: true,
		search: {
			enabled: <?php echo $search; ?>,
			autocomplete: 'off'
		},
		pagination: {
			limit: <?php echo $limit; ?>,
			enabled: <?php echo $paginacion; ?>,
			summary: true,
			className: 'btn btn-success'
		},
		className: {
			table: 'table table-bordered table-hover table-striped',
			search: "form-control",
			thead: "bg-light",
			search: "form-control form-control-sm"
		},
		language: {
			'search': {
				'placeholder': 'Buscar...'
			},
			'pagination': {
				'previous': '<<',
				'next': '>>',
				'showing': 'Mostrando de ',
				'to': 'a',
				'of': 'de',
				'results': () => 'Registros en total'
			}
		},
		data: dataTable,
	}).render(document.getElementById("<?php echo $id; ?>"));
	/*gridjs.on('rowClick', (...args) => console.log('row: ' + JSON.stringify(args), args));
	gridjs.on('cellClick', (...args) => console.log('cell: ' + JSON.stringify(args), args));*/
	/*grid.on('rowClick', function(args) {alert(JSON.stringify(args),args)});
	grid.on('cellClick', function(args) {alert(args[])});*/
</script>

<script>
	<?php echo $js; ?>
</script>