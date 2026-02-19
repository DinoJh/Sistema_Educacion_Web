<div class="modal fade" id="<?php echo $id; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel_<?php echo $id; ?>" aria-hidden="true">
	<div class="modal-dialog <?php echo $size; ?>">
    	<div class="modal-content">
			<div class="modal-header">
	        	<h1 class="modal-title fs-5" id="staticBackdropLabel_<?php echo $id; ?>">
	         		<?php echo $titulo; ?>
	        	</h1>
	        	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	      	</div>
      		<div class="modal-body">
		       	<?php echo $body; ?>
			</div>
	    	<div class="modal-footer">
        		<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        		<?php echo $botonok; ?>
      		</div>
    	</div>
  	</div>
</div>
