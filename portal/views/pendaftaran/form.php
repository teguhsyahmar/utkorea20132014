<link href="<?php echo template_path('core')?>css/core.css" rel="stylesheet" type="text/css"  media='screen'/>

<?php if (isset($message)) echo $message;  else $message= '';?>
<h3>Pendaftaran Mahasiswa Baru Universitas Terbuka Korea 2013</h3>
                <div class="form-box-top"></div>
                <div class="form-box">
                    <div id="contactFormArea">
						                            
                    
<?php echo form_open_multipart(current_url(), array('class'=>'fancy')); ?>
	<fieldset>
	<legend>Data Pribadi</legend>
    <ol>
		<li>
			<label class="element">
				<?php echo $this->lang->line('name')?>
			</label>
			<div class="element">
				<?php echo form_input('name',$this->input->post('name'))?>
				<div class='hint'>Nama Sesuai Ijasah Terakhir</div>				
			</div>
		</li>	
		<li>
			<label class="element">
				<?php echo $this->lang->line('no_passport')?>
			</label>
			<div class="element">
				<?php echo form_input('passport',$this->input->post('passport'))?>				
			</div>
		</li>		
		<li>
			<label class="element">
				<?php echo $this->lang->line('gender')?>
			</label>
			<div class="element">
				<?php
					$gender1 = TRUE;
					$gender2 = FALSE;
					
					if (isset($_POST['gender']) && ($_POST['gender'] == 'Wanita')) 
					{
						$gender1 = FALSE;
						$gender2 = TRUE;
					}
				?>
				<?php echo form_radio('gender','L',$gender1);?>
				<label>Laki-Laki</label>&nbsp;&nbsp;
				<?php echo form_radio('gender','P',$gender2);?>
				<label>Perempuan</label>
			</div>
		</li>
		<li>
			<label class="element">
				<?php echo $this->lang->line('place_time_birth')?>
			</label>
			<div class="element">
				<?php echo form_input('place_of_birth',$this->input->post('place_of_birth'),"style='width:10em'")?>
				<?php
					$day_birth = array();
					foreach (range(1,31) as $v) 
					{
						$i =  sprintf("%02d",$v);
						$day_birth[$i] = $i;
					}
					
					$month_birth = array();
					foreach (range(1,12) as $v)
					{
						$i =  sprintf("%02d",$v);
						$month_birth[$i] = $i;
					}
					
					$year_birth = array();
					
					foreach (range('1960',date('Y')-16) as $v) {
						$year_birth[$v] = $v;
					}
					
					foreach (range('1970',date('Y')) as $v) {
						$year_graduate[$v] = $v;
					}
					
					echo form_dropdown('day_birth',$day_birth,$this->input->post('day_birth'));				
					echo form_dropdown('month_birth',$month_birth,$this->input->post('month_birth'));
					echo form_dropdown('year_birth',$year_birth,$this->input->post('year_birth'));
				?>
				<div class='hint'>Tempat Tanggal Lahir Sesuai Ijasah Terakhir</div>
			</div>
		</li>
		<li>
			<label class="element">
				<?php echo $this->lang->line('address_id')?>
			</label>
			<div class="element">
				<?php echo form_textarea('address_id',$this->input->post('address_id'),"style='width:30em;height:4em'")?>				
			</div>
		</li>
		<li>
			<label class="element">
				<?php echo $this->lang->line('address_kr')?>
			</label>
			<div class="element">
				<?php echo form_textarea('address_kr',$this->input->post('address_kr'),"style='width:30em;height:4em'")?>
			</div>
		</li>
		<li>
			<label class="element">
				<?php echo $this->lang->line('phone')?>
			</label>
			<div class="element">
				+82 <?php echo form_input('phone',$this->input->post('phone'),"maxlength='10' size='10'")?>
				Tanpa diawali 0, misal 1012345678				
			</div>
		</li>
		<li>
			<label class="element">
				<?php echo $this->lang->line('email')?>
			</label>
			<div class="element">
				<?php echo form_input('email',$this->input->post('email'))?>
                * Jangan menggunakan email Yahoo!
			</div>
		</li>
		<li>
			<label class="element">
				<?php echo $this->lang->line('religion')?>
			</label>
			<div class="element">
				<?php echo form_dropdown('religion',lang_list('religion_list'),$this->input->post('religion'));?>
			</div>
		</li>
		<li>
			<label class="element">
				<?php echo $this->lang->line('employment_status')?>
			</label>
			<div class="element">
				<?php echo form_dropdown('employment',lang_list('employment_list'),$this->input->post('employment'));?>
			</div>
		</li>
		<li>
			<label class="element">
				<?php echo $this->lang->line('marital_status')?>
			</label>
			<div class="element">
				<?php echo form_dropdown('marital_status',lang_list('marital_status_list'),$this->input->post('marital_status'));?>				
			</div>
		</li>
		<li>
			<label class="element">
				<?php echo $this->lang->line('last_education')?>
			</label>
			<div class="element">
				<?php echo form_dropdown('last_education',lang_list('education_list'),$this->input->post('last_education'));?>				
			</div>
		</li>
		<li>
			<label class="element">
				<?php echo $this->lang->line('major_code')?>
			</label>
			<div class="element">
				<?php 
				$data_edu = array('name' => 'last_education_major',
								  'value' => $this->input->post('last_education_major'),
								  'style' => 'width:3em',
								  'maxlength' => '3',
								  'readonly' => 'readonly');
				echo form_input($data_edu)?> 
				Klik 
				<a href='javascript://' class='contact'>
					<img src='<?php echo template_path('core')?>images/search.png' alt='' />
				</a>
				Untuk Memilih Jurusan
				<div class='hint'>
					Pilih kode jurusan sesuai jurusan pada ijazah terakhir. Misalnya jika anda lulusan SMA pilih 101, SMEA pilih 104
				</div>				
			</div>
		</li>
		<li>
			<label class="element">
				<?php echo $this->lang->line('year_graduate')?>
			</label>
			<div class="element">
				<?php echo form_dropdown('year_graduate',$year_graduate,$this->input->post('year_graduate'));?>
			</div>
		</li>
		<li>
			<label class="element">
				Sebagai Tenaga Pengajar
			</label>
			<div class="element">
				<?php echo form_dropdown('teach',lang_list('yes_no_list'),$this->input->post('teach'),"id='teach'");?>				
			</div>
		</li>
		<li class="teach_opt">
			<label class="element">
				Jika Ya Mengajar Pada
			</label>
			<div class="element">
				<select name='teach_at' />
					<option value='1'>TK</option>
					<option value='2'>SD</option>
					<option value='3'>SLTP</option>
					<option value='4'>SLTA</option>
					<option value='5'>PT</option>
					<option value='6'>Non Formal</option>
				</select>
			</div>
		</li>
		<li class="teach_opt">
			<label class="element">
				Mengajar Bidang Studi
			</label>
			<div class="element">
				<?php echo form_input('teach_major',$this->input->post('teach_major'))?>
			</div>
		</li>		
		<li>
			<label class="element">
				<?php echo $this->lang->line('mother_name')?>
			</label>
			<div class="element">
				<?php echo form_input('mother_name',$this->input->post('mother_name'))?>
			</div>
		</li>
		<li>
			<label class="element">
				Ijasah Terakhir
			</label>
			<div class="element">
				<input name="ijasah" class="fileupload" type="file" data-url="<?php echo base_url()?>pendaftaran/do_upload/ijasah">
				<input type="hidden" name="ijasah_image" /> 
				Ukuran Maks. 10MB (gif, png, jpg, jpeg)
				<div id='ijasah_cnt' class="image_container"></div>
			</div>
		</li>
		<li>
			<label class="element">
				Foto
			</label>
			<div class="element">
				<input class="fileupload" type="file" name="photo"  data-url="<?php echo base_url()?>pendaftaran/do_upload/photo" >
				<input type="hidden" name="photo_image" />				 
				Ukuran Maks. 10MB (gif, png, jpg, jpeg)
				<div id='photo_cnt' class="image_container"></div>
			</div>			
		</li>
		</ol>
	</fieldset>
	<fieldset>
		<legend>Universitas Terbuka Korea Selatan</legend>
		<ol>
		<li>
			<label class="element">
				Jurusan
			</label>
			<div class="element">
				<?php echo form_dropdown('major',major_list())?>				
			</div>
		</li>
		<li>
			<label class="element">
				Lokasi Perkuliahan
			</label>
			<div class="element">
				<select name="region">
					<option value="1">Ansan (Untuk daerah Seoul, Ansan, dan sekitarnya)</option>
					<option value="2">Daegu (Untuk daerah Daegu, Busan, Yeongnam, Daejeon dan sekitarnya)</option>
				</select>				
			</div>
		</li>
		<li>
			<label class="element">
				&nbsp;
			</label>
			<div class="element">
				<input type="submit" name="submitRegBtn" value="Submit" class="fancy_button" />
			</div>
		</li>
	</ol>	       
    </fieldset>    
</form>
</div>
                      
                </div>
                <div class="form-box-bottom"></div>

				
<div id="edu_list-form"></div>
<div style='display:none'>
	<img src='<?php echo template_path('core')?>images/x.png' alt='' />
</div>
		
<script type='text/javascript' src="<?php echo template_path('core')?>js/jquery.ui.widget.js"></script>
<script type='text/javascript' src="<?php echo template_path('core')?>js/jquery.iframe-transport.js"></script>
<script type='text/javascript' src="<?php echo template_path('core')?>js/jquery.fileupload.js"></script>
<script type='text/javascript' src="<?php echo template_path('core')?>js/jquery.validate.min.js"></script>
<script type='text/javascript' src="<?php echo template_path('core')?>js/jquery.simplemodal.js"></script>
<script type='text/javascript' src="<?php echo template_path('core')?>js/new_registration.js"></script>
<script type='text/javascript' src="<?php echo template_path('core')?>js/check_edu.js"></script>
<script type="text/javascript" >
	$(document).ready(function(){
		$('.fileupload').fileupload({
			dataType: 'json',
			maxFileSize: 10000,
			acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
			progress: function () {
				var loader = $(this).attr('name') +'_loader';
				$(this).after(" <img src='<?php echo template_path('triveo')?>images/loading.gif' class='"+ loader +"' /> ");
			},
			error: function (e, data) {
				alert("Error");
			},
			done: function (e, data) {					
				var cont = $(this).attr('name') +'_cnt';
				var loader = $(this).attr('name') +'_loader';
				var hidden_field = $(this).attr('name') +'_image';
				$(this).after("<img src='<?php echo template_path('triveo')?>images/tick_small.png' />");
				
				$('.'+ loader).hide();
				$.each(data.result, function (index, file) {
					
					$("<img src='"+ file.thumbnail_url +"'/>").appendTo('#' + cont);						
					$("input[name=" + hidden_field + "]").val(file.name);
				});
			}
		});	
	})
</script>
