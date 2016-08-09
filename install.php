<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CI S3 Integration Configuration</title>
<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
</head>
<body>
<?php
if(isset($_POST['submit'])) { 

	$access_key = $_POST['aws_accesskey'];
	$secret_key = $_POST['aws_secretkey'];
	$bucket_name = $_POST['bucket_name'];
	
	$str=implode(file('./application/libraries/cisssintegration_lib.php'));
	$fp = @fopen('./application/libraries/cisssintegration_lib.php','w');
	if(!$fp)
	{
		?>
		<div class="clearfix" style="width:50%; margin:0 auto;padding-top:50px;" >
		  <div id="build">
		<span class="label label-warning">Unable to open application/libraries/cisssintegration_lib.php file to write configuration,<br/> please ensure that specified file has write permission and tr again or alternatively you can open the file manually and write the specified params.</span>
		</div>
		</div>
		<?php		
		return;
	}
	
	$str=str_replace('{{AWS_ACCESS_KEY}}',$access_key,$str);
	$str=str_replace('{{AWS_SECRET_KEY}}',$secret_key,$str);
	$str=str_replace('{{BUCKET_NAME}}',$bucket_name,$str);
	fwrite($fp,$str,strlen($str));
	?>
    <div class="clearfix" style="width:50%; margin:0 auto;padding-top:50px;" >
      
      <div id="build">
    <span class='label label-success'>Congratulations you have successfully configured the CI S3 Integration setting</span>
    </div>
    </div>
	<?php			
	return;

}
			
?>
<div class="clearfix" style="width:50%; margin:0 auto;padding-top:50px;" >
  
  <div id="build">
    <form id="target" class="form-horizontal" action="" method="post">
      <fieldset>
        <div class="component" data-content="&lt;form class='form'&gt;
  &lt;div class='controls'&gt;
    
      
      
    &lt;label class='control-label'&gt; Form Name &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='name' id='name' value ='CI S3 Integration Configuration' /&gt;

    &lt;hr/&gt;
    &lt;button id=&quot;save&quot; class='btn btn-info'&gt;Save&lt;/button&gt;&lt;button id=&quot;cancel&quot; class='btn btn-danger'&gt;Cancel&lt;/button&gt;
  &lt;/div&gt;
&lt;/form&gt;
" data-title="Form Name" data-trigger="manual" data-html="true"><!-- Form Name -->
          <legend>CI S3 Integration Configuration</legend>
        </div>
        <div class="component" data-content="&lt;form class='form'&gt;
  &lt;div class='controls'&gt;
    
      
      
      
      
      
      
      
      
      
      
      
      
    &lt;label class='control-label'&gt; ID / Name &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='id' id='id' value ='aws_accesskey' /&gt;
&lt;label class='control-label'&gt; Label Text &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='label' id='label' value ='AWS Access Key' /&gt;
&lt;label class='control-label'&gt; Placeholder &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='placeholder' id='placeholder' value ='AWS Access Key' /&gt;
&lt;label class='control-label'&gt; Help Text &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='helptext' id='helptext' value ='AWS account Access Key' /&gt;
&lt;label class=&quot;checkbox control-group&quot;&gt;
  &lt;input type=&quot;checkbox&quot; data-type=&quot;checkbox&quot; class=&quot;input-inline field&quot; name=&quot;required&quot; id=&quot;required&quot; checked=&quot;checked&quot; &gt;
  Required
&lt;/label&gt;
&lt;label class='control-label'&gt; Input Size &lt;/label&gt;
&lt;select class=&quot;field&quot; data-type=&quot;select&quot; id='inputsize'&gt;

  &lt;option value=&quot;input-mini&quot;  &gt;Mini&lt;/option&gt;

  &lt;option value=&quot;input-small&quot;  &gt;Small&lt;/option&gt;

  &lt;option value=&quot;input-medium&quot;  &gt;Medium&lt;/option&gt;

  &lt;option value=&quot;input-large&quot;  &gt;Large&lt;/option&gt;

  &lt;option value=&quot;input-xlarge&quot;  selected  &gt;Xlarge&lt;/option&gt;

  &lt;option value=&quot;input-xxlarge&quot;  &gt;Xxlarge&lt;/option&gt;

&lt;/select&gt;

    &lt;hr/&gt;
    &lt;button id=&quot;save&quot; class='btn btn-info'&gt;Save&lt;/button&gt;&lt;button id=&quot;cancel&quot; class='btn btn-danger'&gt;Cancel&lt;/button&gt;
  &lt;/div&gt;
&lt;/form&gt;
" data-title="Text Input" data-trigger="manual" data-html="true"><!-- Text input-->
          <div class="control-group">
            <label class="control-label" for="aws_accesskey">AWS Access Key</label>
            <div class="controls">
              <input id="aws_accesskey" name="aws_accesskey" type="text" placeholder="AWS Access Key" class="input-xlarge" required="">
              <p class="help-block">AWS account Access Key</p>
            </div>
          </div>
        </div>
        <div class="component" data-content="&lt;form class='form'&gt;
  &lt;div class='controls'&gt;
    
      
      
      
      
      
      
      
      
      
      
      
      
    &lt;label class='control-label'&gt; ID / Name &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='id' id='id' value ='aws_secretkey' /&gt;
&lt;label class='control-label'&gt; Label Text &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='label' id='label' value ='AWS Secret Key' /&gt;
&lt;label class='control-label'&gt; Placeholder &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='placeholder' id='placeholder' value ='AWS Secret Key' /&gt;
&lt;label class='control-label'&gt; Help Text &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='helptext' id='helptext' value ='AWS account Secret Key' /&gt;
&lt;label class=&quot;checkbox control-group&quot;&gt;
  &lt;input type=&quot;checkbox&quot; data-type=&quot;checkbox&quot; class=&quot;input-inline field&quot; name=&quot;required&quot; id=&quot;required&quot; checked=&quot;checked&quot; &gt;
  Required
&lt;/label&gt;
&lt;label class='control-label'&gt; Input Size &lt;/label&gt;
&lt;select class=&quot;field&quot; data-type=&quot;select&quot; id='inputsize'&gt;

  &lt;option value=&quot;input-mini&quot;  &gt;Mini&lt;/option&gt;

  &lt;option value=&quot;input-small&quot;  &gt;Small&lt;/option&gt;

  &lt;option value=&quot;input-medium&quot;  &gt;Medium&lt;/option&gt;

  &lt;option value=&quot;input-large&quot;  &gt;Large&lt;/option&gt;

  &lt;option value=&quot;input-xlarge&quot;  selected  &gt;Xlarge&lt;/option&gt;

  &lt;option value=&quot;input-xxlarge&quot;  &gt;Xxlarge&lt;/option&gt;

&lt;/select&gt;

    &lt;hr/&gt;
    &lt;button id=&quot;save&quot; class='btn btn-info'&gt;Save&lt;/button&gt;&lt;button id=&quot;cancel&quot; class='btn btn-danger'&gt;Cancel&lt;/button&gt;
  &lt;/div&gt;
&lt;/form&gt;
" data-title="Text Input" data-trigger="manual" data-html="true"><!-- Text input-->
          <div class="control-group">
            <label class="control-label" for="aws_secretkey">AWS Secret Key</label>
            <div class="controls">
              <input id="aws_secretkey" name="aws_secretkey" type="text" placeholder="AWS Secret Key" class="input-xlarge" required="">
              <p class="help-block">AWS account Secret Key</p>
            </div>
          </div>
        </div>
        <div class="component" data-content="&lt;form class='form'&gt;
  &lt;div class='controls'&gt;
    
      
      
      
      
      
      
      
      
      
      
      
      
    &lt;label class='control-label'&gt; ID / Name &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='id' id='id' value ='bucket_name' /&gt;
&lt;label class='control-label'&gt; Label Text &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='label' id='label' value ='Bucket Name' /&gt;
&lt;label class='control-label'&gt; Placeholder &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='placeholder' id='placeholder' value ='Bucket Name' /&gt;
&lt;label class='control-label'&gt; Help Text &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='helptext' id='helptext' value ='AWS S3 bucket name you want to use with this project' /&gt;
&lt;label class=&quot;checkbox control-group&quot;&gt;
  &lt;input type=&quot;checkbox&quot; data-type=&quot;checkbox&quot; class=&quot;input-inline field&quot; name=&quot;required&quot; id=&quot;required&quot; checked=&quot;checked&quot; &gt;
  Required
&lt;/label&gt;
&lt;label class='control-label'&gt; Input Size &lt;/label&gt;
&lt;select class=&quot;field&quot; data-type=&quot;select&quot; id='inputsize'&gt;

  &lt;option value=&quot;input-mini&quot;  &gt;Mini&lt;/option&gt;

  &lt;option value=&quot;input-small&quot;  &gt;Small&lt;/option&gt;

  &lt;option value=&quot;input-medium&quot;  &gt;Medium&lt;/option&gt;

  &lt;option value=&quot;input-large&quot;  &gt;Large&lt;/option&gt;

  &lt;option value=&quot;input-xlarge&quot;  selected  &gt;Xlarge&lt;/option&gt;

  &lt;option value=&quot;input-xxlarge&quot;  &gt;Xxlarge&lt;/option&gt;

&lt;/select&gt;

    &lt;hr/&gt;
    &lt;button id=&quot;save&quot; class='btn btn-info'&gt;Save&lt;/button&gt;&lt;button id=&quot;cancel&quot; class='btn btn-danger'&gt;Cancel&lt;/button&gt;
  &lt;/div&gt;
&lt;/form&gt;
" data-title="Text Input" data-trigger="manual" data-html="true"><!-- Text input-->
          <div class="control-group">
            <label class="control-label" for="bucket_name">Bucket Name</label>
            <div class="controls">
              <input id="bucket_name" name="bucket_name" type="text" placeholder="Bucket Name" class="input-xlarge" required="">
              <p class="help-block">AWS S3 bucket name you want to use with this project</p>
            </div>
          </div>
        </div>
        <div class="component" data-content="&lt;form class='form'&gt;
  &lt;div class='controls'&gt;
    
      
      
      
      
      
      
      
      
    &lt;label class='control-label'&gt; ID / Name &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='id' id='id' value ='submit' /&gt;
&lt;label class='control-label'&gt; Label Text &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='label' id='label' value ='' /&gt;
&lt;label class='control-label'&gt; Button Label &lt;/label&gt;
&lt;input class='input-large field' data-type=&quot;input&quot; type='text' name='buttonlabel' id='buttonlabel' value ='Submit' /&gt;
&lt;label class='control-label'&gt; Button Type &lt;/label&gt;
&lt;select class=&quot;field&quot; data-type=&quot;select&quot; id='buttontype'&gt;

  &lt;option value=&quot;btn-default&quot;  &gt;Default&lt;/option&gt;

  &lt;option value=&quot;btn-primary&quot;  &gt;Primary&lt;/option&gt;

  &lt;option value=&quot;btn-info&quot;  &gt;Info&lt;/option&gt;

  &lt;option value=&quot;btn-success&quot;  selected  &gt;Success&lt;/option&gt;

  &lt;option value=&quot;btn-warning&quot;  &gt;Warning&lt;/option&gt;

  &lt;option value=&quot;btn-danger&quot;  &gt;Danger&lt;/option&gt;

  &lt;option value=&quot;btn-inverse&quot;  &gt;Inverse&lt;/option&gt;

&lt;/select&gt;

    &lt;hr/&gt;
    &lt;button id=&quot;save&quot; class='btn btn-info'&gt;Save&lt;/button&gt;&lt;button id=&quot;cancel&quot; class='btn btn-danger'&gt;Cancel&lt;/button&gt;
  &lt;/div&gt;
&lt;/form&gt;
" data-title="Single Button" data-trigger="manual" data-html="true"><!-- Button -->
          <div class="control-group">
            <label class="control-label" for="submit"></label>
            <div class="controls">
              <button id="submit" name="submit" class="btn btn-success">Submit</button>
            </div>
          </div>
        </div>
      </fieldset>
    </form>
  </div>
</div>
</body>
</html>