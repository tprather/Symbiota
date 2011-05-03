<?php
 include_once('../config/symbini.php');
 include_once($serverRoot.'/classes/OccurrenceManager.php');
 header("Content-Type: text/html; charset=".$charset);

 $catId = array_key_exists("catid",$_REQUEST)?$_REQUEST["catid"]:0;
 
 $collManager = new OccurrenceManager();
 $collManager->reset();
 
 $specArr = Array();
 $obsArr = Array();
 $collList = $collManager->getCollectionArr($catId);
 foreach($collList as $collId => $collObj){
	$collType = $collObj["colltype"];
	if(stripos($collType, "specimen") !== false){
	 	$specArr[$collId]["institutioncode"] = $collObj["institutioncode"];
	 	$specArr[$collId]["collectionname"] = $collObj["collectionname"];
	 	$specArr[$collId]["icon"] = $collObj["icon"];
	}
	elseif(stripos($collType, "observation") !== false){
	 	$obsArr[$collId]["institutioncode"] = $collObj["institutioncode"];
		$obsArr[$collId]["collectionname"] = $collObj["collectionname"];
	 	$obsArr[$collId]["icon"] = $collObj["icon"];
	}
 } 
 $otherCatArr = $collManager->getSurveys();
 //$ownerInstArr = $collManager->getOwnerInstitutions();
 //$specProjArr = $collManager->getSpecProjects();
 ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset;?>">
		<title><?php echo $defaultTitle; ?> Collections Search</title>
	    <link type="text/css" href="../css/main.css" rel="stylesheet" />
		<script type="text/javascript">
			<?php include_once($serverRoot.'/config/googleanalytics.php'); ?>
		</script>
		<link type="text/css" href="../css/jquery-ui.css" rel="Stylesheet" />	
		<script type="text/javascript" src="../js/jquery-1.4.4.min.js"></script>
		<script type="text/javascript" src="../js/jquery-ui-1.8.11.custom.min.js"></script>
		<script language="javascript" type="text/javascript">
			$(document).ready(function() {
				$('#tabs').tabs();

				c = document.cookie;
				if(c.indexOf("colldbs=all") > -1){
					document.getElementById("dballcb").checked = true;
				}

		        document.collections.onkeydown = checkKey;
			});
		
			function selectAll(thisCB){
				cName = thisCB.className;
				boxesChecked = true;
				if(!thisCB.checked){
					boxesChecked = false;
				}
				var dbElements = document.getElementsByName("db[]");
				for(i = 0; i < dbElements.length; i++){
					var dbElement = dbElements[i];
					if(dbElement.className = cName){
						dbElement.checked = boxesChecked;
					}
					else{
						dbElement.checked = false;
					}
				}
			}
		
		    function checkForm(){
				var dbElements = document.getElementsByName("db[]");
				for(i = 0; i < dbElements.length; i++){
					var dbElement = dbElements[i];
					if(dbElement.checked) return true;
				}
			   	alert("Please choose at least one database!");
		      	return false;
		    }
		    
		    function checkKey(e){
		        var key;
		        if(window.event){
		            key = window.event.keyCode;
		        }else{
		            key = e.keyCode;
		        }
		        if(key == 13){
		            document.collections.submit();
		        }
		    }
		    
		</script>
	</head>
	<body>
	
	<?php
	$displayLeftMenu = (isset($collections_indexMenu)?$collections_indexMenu:false);
	include($serverRoot."/header.php");
	if(isset($collections_indexCrumbs)){
		if($collections_indexCrumbs){
			echo "<div class='navpath'>";
			echo "<a href='../index.php'>Home</a> &gt; ";
			echo $collections_indexCrumbs;
			echo " <b>Collections</b>";
			echo "</div>";
		}
	}
	else{
		echo "<div class='navpath'>";
		echo "<a href='../index.php'>Home</a> &gt; ";
		echo "<b>Collections</b>";
		echo "</div>";
	}
	?>
	<!-- This is inner text! -->
	<div id="innertext">
		<h1>Select Collections to be Searched</h1>
		<div id="tabs" style="margin:0px;">
		    <ul>
		        <?php if($specArr && $obsArr){?>
		        <li><a href="#specobsdiv">Specimens &amp; Observations</a></li>
		        <?php }if($specArr){?>
		        <li><a href="#specimendiv">Specimens</a></li>
		        <?php }if($obsArr){?>
		        <li><a href="#observationdiv">Observations</a></li>
		        <?php }if($otherCatArr){?>
		        <li><a href="#otherdiv">Other Categories</a></li>
		        <?php } ?>
		    </ul>
			<form name="collections" id="collform" action="harvestparams.php" method="get" onsubmit="return checkForm()">
	        <?php 
	        if($specArr && $obsArr){
	        	?>
				<div id="specobsdiv">
					<table width="90%">
						<tr>
							<td colspan="4">
					        	<div style="margin:0px 0px 10px 30px;">
					         		<input id="dballcb" name="db[]" class="specobs" value='all' type="checkbox" onclick="selectAll(this);" />
					         		Select/Deselect all <a href="<?php echo $clientRoot; ?>/collections/misc/collprofiles.php">Collections</a>
					        	</div>
							</td>
						</tr>
						<?php
						$collCnt = 1;
						foreach($collList as $collId => $collArr){
							?>
						    <tr>
								<td width="50px">
							    	<a href = 'misc/collprofiles.php?collid=<?php echo $collId; ?>' target='_blank'>
							    		<img border='1' height='30' width='30' src='../<?php echo $collArr["icon"];?>'>
							    	</a>
							    </td>
							    <td width="30px">
						    		<input name="db[]" class="specobs" value='<?php echo $collId; ?>' type='checkbox' <?php echo (array_key_exists("isselected",$collArr)?"CHECKED":""); ?> /> 
							    </td>
							    <td>
						    		<a href = 'misc/collprofiles.php?collid=<?php echo $collId; ?>' target='_blank' style='text-decoration:none;color:black;font-size:120%;'>
						    			<?php echo $collArr["collectionname"]." (".$collArr["institutioncode"].")"; ?>
						    		</a>
							    </td>
							    <td width="60px" align="center">
							    	<?php if($collCnt%8 == 4 || (count($collList) < 4 && $collCnt == 1)){ ?>
						        	<input type="image" src='../images/next1.gif'
						                onmouseover="javascript:this.src = '../images/next1_roll.gif';" 
						                onmouseout="javascript:this.src = '../images/next1.gif';"
						                title="Click button to advance to the next step" />
							    	<?php } ?>
						    	</td>
						    </tr>
						    <?php
						    $collCnt++; 
						}
						?>
					</table>
				</div>
	        <?php 
	        }
	        if($specArr){
	        	?>
				<div id="specimendiv">
					<table width="90%">
						<tr>
							<td colspan="4">
					        	<div style="margin:0px 0px 10px 30px;">
					         		<input name="db[]" class="spec" value='' type="checkbox" onclick="javascript:selectAll(this);" />
					         		Select/Deselect all <a href="<?php echo $clientRoot; ?>/collections/misc/collprofiles.php">Collections</a>
					        	</div>
							</td>
						</tr>
						<?php
						$collCnt = 1;
						foreach($specArr as $collId => $collArr){
							?>
						    <tr>
								<td width="50px">
							    	<a href = 'misc/collprofiles.php?collid=<?php echo $collId; ?>' target='_blank'>
							    		<img border='1' height='30' width='30' src='../<?php echo $collArr["icon"];?>'>
							    	</a>
							    </td>
							    <td width="30px">
						    		<input name="db[]" class="spec" value='<?php echo $collId; ?>' type='checkbox' <?php echo (array_key_exists("isselected",$collArr)?"CHECKED":""); ?> /> 
							    </td>
							    <td>
						    		<a href = 'misc/collprofiles.php?collid=<?php echo $collId; ?>' target='_blank' style='text-decoration:none;color:black;font-size:120%;'>
						    			<?php echo $collArr["collectionname"]." (".$collArr["institutioncode"].")"; ?>
						    		</a>
							    </td>
							    <td width="60px" align="center">
							    	<?php if($collCnt%8 == 4 || (count($specArr) < 4 && $collCnt == 1)){ ?>
						        	<input type="image" src='../images/next1.gif'
						                onmouseover="javascript:this.src = '../images/next1_roll.gif';" 
						                onmouseout="javascript:this.src = '../images/next1.gif';"
						                title="Click button to advance to the next step" />
							    	<?php } ?>
						    	</td>
						    </tr>
						    <?php
						    $collCnt++; 
						}
						?>
					</table>
				</div>
	        	<?php 
	        }
	        if($obsArr){
	        	?>
				<div id="observationdiv">
					<table width="90%">
						<tr>
							<td colspan="4">
					        	<div style="margin:0px 0px 10px 30px;">
					         		<input name="db[]" class="obs" value='' type="checkbox" onclick="javascript:selectAll(this,'obstable');" />
					         		Select/Deselect all <a href="<?php echo $clientRoot; ?>/collections/misc/collprofiles.php">Collections</a>
					        	</div>
							</td>
						</tr>
						<?php
						$collCnt = 1;
						foreach($obsArr as $collId => $collArr){
							?>
						    <tr>
								<td width="50px">
							    	<a href = 'misc/collprofiles.php?collid=<?php echo $collId; ?>' target='_blank'>
							    		<img border='1' height='30' width='30' src='../<?php echo $collArr["icon"];?>'>
							    	</a>
							    </td>
							    <td width="30px">
						    		<input name="db[]" class="obs" value='<?php echo $collId; ?>' type='checkbox' <?php echo (array_key_exists("isselected",$collArr)?"CHECKED":""); ?> /> 
							    </td>
								<td>
						    		<a href = 'misc/collprofiles.php?collid=<?php echo $collId; ?>' target='_blank' style='text-decoration:none;color:black;font-size:120%;'>
						    			<?php echo $collArr["collectionname"]." (".$collArr["institutioncode"].")"; ?>
						    		</a>
								</td>
								<td width="60px" align="center">
							    	<?php if($collCnt%8 == 4 || (count($obsArr) < 4 && $collCnt == 1)){ ?>
						        	<input type="image" src='../images/next1.gif'
						                onmouseover="javascript:this.src = '../images/next1_roll.gif';" 
						                onmouseout="javascript:this.src = '../images/next1.gif';"
						                title="Click button to advance to the next step" />
							    	<?php } ?>
							    	<input type="hidden" name="catid" value="<?php echo $catId; ?>" /> 
						    	</td>
						    </tr>
						    <?php
						    $collCnt++; 
						}
						?>
					</table>
				</div>
				<?php 
	        } 
	        ?>
			</form>
			<?php 
			if($otherCatArr){ 
				?>
				<div id="otherdiv">
					<form id="othercatform" action="harvestparams.php" method="get">
						<?php 
						foreach($otherCatArr as $projTitle => $surveyArr){
							?>
							<fieldset style="margin:10px;">
								<legend style="font-weight:bold;"><?php echo $projTitle; ?></legend>
								<div style="margin:10px;">
									<?php 
									foreach($surveyArr as $surveyId => $surveyName){
										?>
										<div style="margin:5px;">
											<input name="surveyid[]" value='<?php echo $surveyId; ?>' type='checkbox' />
											<?php echo $surveyName; ?>
										</div>
										<?php 
									}
									?>
									<div style="margin:15px;">
										<input type="submit" name="action" value="Submit Query" />
									</div>
								</div>
							</fieldset>
							<?php 
						}
						?>
					</form>
				</div>
				<?php 
			} 
			?>
		</div>
	</div>
	<?php
	include($serverRoot."/footer.php");
	?>
	</body>
</html>