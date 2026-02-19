<?php namespace App\Libraries;
 
class Formulario{
	function __construct(){
        
    }
    public function crear($label,$type,$size,$name,$value=false,$disabled=false){
    	for($i=0; $i <count($name) ; $i++){
    		if($type[$i]=="texto" or $type[$i]=="dni" or $type[$i]=="entero" or $type[$i]=="email" or $type[$i]=="celular" or $type[$i]=="user"){
	    		echo "
		        <div class='col-md-".$size[$i]."'>
		        	<div class='form-group'>
		            	<label>".$label[$i]."</label>
		            		<input 
		                    	type='text' 
		                        class='form-control form-control-sm' 
		                        name='".$name[$i]."' value='".$value[$i]."'
		                        tipo='".$type[$i]."'
		                        required='".(is_array($disabled)? $disabled[$i] :'disabled' )."'
							>
	                </div>
	            </div>";
			}
			if($type[$i]=="numero"){
	    		echo "
		        <div class='col-md-".$size[$i]."'>
		        	<div class='form-group'>
		            	<label>".$label[$i]."</label>
		            		<input 
		                    	type='number'
		                        class='form-control form-control-sm' 
		                        name='".$name[$i]."' value='".$value[$i]."'
		                        tipo='".$type[$i]."'
		                        required='".(is_array($disabled)? $disabled[$i] :'disabled' )."'
							>
	                </div>
	            </div>";
	        }
	        
	        if($type[$i]=="password"){
	    		echo "
		        <div class='col-md-".$size[$i]."'>
		        	<div class='form-group'>
	                    <label >".$label[$i]."</label>
		            	
							<input 
		                    	type='password' 
		                        class='form-control form-control-sm' 
		                        name='".$name[$i]."' value='".$value[$i]."'
		                        tipo='".$type[$i]."'
		                        required='".(is_array($disabled)? $disabled[$i] :'disabled' )."'
							>
	                </div>
	            </div>";
	        }
	        
	        else if($type[$i]=="lista"){
	        	echo "
		        <div class='col-md-".$size[$i]."'>
		        	<div class='form-group'>
		            	<label>".$label[$i]."</label>
								<select 
								class='form-control form-control-sm'  name='".$name[$i]."' tipo='".$type[$i]."' required>
	                            	<option value=''>Seleccione</option>";
	                                foreach ($value[$i] as $reg){
	                                	echo "
	                                		<option value='".$reg->ide."'>".$reg->nombre."</option>
	                                	";
	                                }
	            echo "
	                            </select>
						</div>
	            </div>";
	        }
	        else if($type[$i]=="fecha"){
	    		echo "
		        <div class='col-md-".$size[$i]."'>
	                    <label >".$label[$i]."</label>

		        	<div class='form-group input-group datetimepicker2'>
	                    	<input 
		                    	type='date' 
		                        class='form-control form-control-sm  text-center' 
		                        name='".$name[$i]."' value='".$value[$i]."'
		                        tipo='".$type[$i]."'
		                        required
							>
	                </div>
	            </div>";
	        }
	        else if($type[$i]=="fechahora"){
	    		echo "
		        <div class='col-md-".$size[$i]."'>
	                    <label >".$label[$i]."</label>

		        	<div class='form-group input-group datetimepicker2'>
							<span class='input-group-addon '><i class='fa fa-calendar'></i></span>
	                    	<input 
		                    	type='text' 
		                        class='form-control form-control-sm  text-center' 
		                        name='".$name[$i]."' value='".$value[$i]."'
		                        tipo='".$type[$i]."'
		                        required
							>
	                </div>
	            </div>";
	        }
	        else if($type[$i]=="hora"){
	    		echo "
		        <div class='col-md-".$size[$i]."'>
                    <label class='fg-label'>".$label[$i]."</label>
		    			<div class='form-group datetimepicker input-group' >
							
							<div class='input-group-prepend'>
								<div class='input-group-text'><i class='fa fa-clock-o'></i></div>
							</div>
							<input 
		                    	type='time' 
		                        class='form-control form-control-sm text-center' 
		                        name='".$name[$i]."' value='".$value[$i]."'
		                        tipo='".$type[$i]."'
		                        required
							>
	                </div>
	            </div>";
	        }
	        else if($type[$i]=="foto"){
	    		echo "
		        	<div class='fileinput fileinput-new col-".$size[$i]."' data-provides='fileinput'>
						<div class='fileinput-preview thumbnail' data-trigger='fileinput' style='line-height: 150px;'></div>
						<div class='col-12 text-center'>
							<div id='image-holder'></div>
							<label for='images'><strong>Añadir Foto</strong></label>
							<div>
								<p class='btn btn-info btn-sm' disabled='><i class='fa fa-plus'></i> ".$label[$i]."</p> 
								<input type='file'  id='fileUpload' name='image' me='".$name[$i]."' style='opacity: 0;margin-top: -60px;width: 100%; height: 40px; vertical-align: middle;' class='form-control text-uppercase' tipo='".$type[$i]."' required> 
							</div> 
						</div>
                        	
                    </div>
		        ";
	        }
	        
	        else if($type[$i]=="fotos"){
	    		echo "
		        	<div class='fileinput fileinput-new col-".$size[$i]."' data-provides='fileinput'>
						<div class='fileinput-preview thumbnail' data-trigger='fileinput' style='line-height: 150px;'></div>
						<div class='col-12 text-center'>
							<div id='image-holder'></div>
							<label for='images'><strong>Añadir Foto</strong></label>
							<div>
								<p class='btn btn-info btn-sm' disabled='><i class='fa fa-plus'></i> ".$label[$i]."</p> 
								<input type='file'  id='fileUpload' name='image[]' e='".$name[$i]."' style='opacity: 0;margin-top: -60px;width: 100%; height: 40px; vertical-align: middle;' class='form-control text-uppercase' tipo='".$type[$i]."' required> 
							</div> 
						</div>
                        	
                    </div>
		        ";
	        }
	        
			else if($type[$i]=="file2"){
	    		echo "
		        	<div class='fileinput fileinput-new col-xs-".$size[$i]."' data-provides='fileinput'>
						<div class='fileinput-preview thumbnail' data-trigger='fileinput' style='line-height: 150px;'>
						</div>
						<div>
							<span class='fileinput-exists'>	Cambiar ".$label[$i]."</span>
                        	<input type='file' name='".$name[$i]."' tipo='".$type[$i]."' required>
							
                            <a href='#'' class='btn btn-sm fileinput-exists waves-effect' data-dismiss='fileinput'>
                            	Borrar
                            </a>
						</div>
                    </div>
		        ";
	        }
			
			else if($type[$i]=="file"){
	    		echo "
				<div class='col-md-".$size[$i]."'>				
					<div class='form-group'>
						<label for='exampleFormControlFile1'>".$label[$i]."</label>
						<input type='file' class='form-control-file' id='fileUpload' name='".$name[$i]."'>
					</div>
			  </div>	
		        "
				;
	        }
            else if($type[$i]=="textarea"){
	    		echo "
		        <div class='col-md-".$size[$i]."'>
		        	<div class='form-group'>
		            	<label>".$label[$i]."</label>
		            		<textarea 
		                    	type='text' row='5'
		                        class='form-control form-control-sm' 
		                        name='".$name[$i]."' 
		                        tipo='".$type[$i]."'
		                        required='".(is_array($disabled)? $disabled[$i] :'disabled' )."'
							>".$value[$i]."</textarea>
	                </div>
	            </div>";
	        }
			
        }
    }
}