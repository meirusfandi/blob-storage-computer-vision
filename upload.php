<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Blob Storage Computer Vision | Submission Azure Cloud Academy</title>
    <link rel="stylesheet" type="text/css" href="css/app.css"/>
    <script type="text/javascript" src="jquery.min.js"></script>
</head>
<body>

    <script type="text/javascript">
        function processImage(){
            var subscriptionKey = "db80b96ff6d0481b8525b516a219cfaa";

            var uriBase = "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";

            var params = {
                "visualFeatures": "Categories,Description,Color",
                "details": "",
                "language": "en",
            };

            // Display the image.
            var sourceImageUrl = document.getElementById("inputImage").value;
            document.querySelector("#sourceImage").src = sourceImageUrl;

            // Make the REST API call.
            $.ajax({
                url: uriBase + "?" + $.param(params),
    
                // Request headers.
                beforeSend: function(xhrObj){
                    xhrObj.setRequestHeader("Content-Type","application/json");
                    xhrObj.setRequestHeader(
                        "Ocp-Apim-Subscription-Key", subscriptionKey);
                },
    
                type: "POST",
    
                // Request body.
                data: '{"url": ' + '"' + sourceImageUrl + '"}',
            })
    
            .done(function(data) {
                // Show formatted JSON on webpage.
                $("#responseTextArea").val(JSON.stringify(data, null, 2));
            })
    
            .fail(function(jqXHR, textStatus, errorThrown) {
                // Display error message.
                var errorString = (errorThrown === "") ? "Error. " :
                    errorThrown + " (" + jqXHR.status + "): ";
                errorString += (jqXHR.responseText === "") ? "" :
                    jQuery.parseJSON(jqXHR.responseText).message;
                alert(errorString);
            });
        };
    </script>

    <div class="container">
		<div class="card">
			<div class="card-body">

                <h2 class="text-center">Hasil Upload File - Mei Rusfandi</h2>
                <hr>
                <h3>View Image To Analize using Computer Vision and Analyze it</h3>
                    
                <?php 
                    require_once 'vendor/autoload.php';
                    require_once "./random_string.php";

                    use MicrosoftAzure\Storage\Blob\BlobRestProxy;
                    use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
                    use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
                    use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
                    use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

                    // $connect_string = "DefaultEndpointsProtocol=https;AccountName=".getenv("ACCOUNT_NAME").";AccountKey=".getenv("ACCOUNT_KEY").";EndpointSuffix=core.windows.net";
                    $connect_string = "DefaultEndpointsProtocol=https;AccountName=fansdev;AccountKey=QFChV4ExeYoe/GCcpbnAagmKnFOvW8y7Lu3dwjyhhnrk/u38o9rLyjoFNXtMLPAO4dKDayHl+nxQPn+jtwKpow==;EndpointSuffix=core.windows.net";

                    //create blob client service
                    $blob_client = BlobRestProxy::createBlobService($connect_string);

                    $create_container_options = new CreateContainerOptions();
                    $create_container_options->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

                    //setup metadata container
                    $create_container_options->addMetaData("key1", "value1");
                    $create_container_options->addMetaData("key2", "value2");

                    //create container name
                    $container_name = "submission".generateRandomString();

                    if (($_POST['upload'])){
                        $ekstensi_diperbolehkan	= array('png','jpg', 'JPG', 'jpeg');
                        $nama = $_FILES['image']['name'];
                        $x = explode('.', $nama);
                        $ekstensi = strtolower(end($x));
                        $ukuran	= $_FILES['image']['size'];
                        $file_tmp = $_FILES['image']['tmp_name'];
                        
                        if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
                            if($ukuran < 1044070){			
                                // move_uploaded_file($file_tmp, 'files/'.$nama);
                                
                                try {
                                    //container create
                                    $blob_client->createContainer($container_name, $create_container_options);

                                    $upload = fopen($nama, "w") or die("Unable to upload file");
                                    fclose($upload);

                                    # Mengunggah file sebagai block blob
                                    echo "Uploading BlockBlob: ".PHP_EOL;
                                    echo $nama;
                                    echo "<br />";
                                    $content = fopen($nama, "r");

                                    //upload to container and blob
                                    $blob_client->createBlockBlob($container_name, $nama, $content);

                                    echo "Upload File Successfully!!!<br/>";

                                    // get list blobs
                                    $bloblists = new ListBlobsOptions();
                                    $bloblists->setPrefix("Final Submission");

                                    $urlImage = "https://fansdev.blob.core.windows.net/".$container_name."/".$nama;

                                    echo "These image from upload: ";
                                    echo "<br/>";
                                    echo "The url image is : https://fansdev.blob.core.windows.net/".$container_name."/".$nama;
                                    echo "<br/>";
                                    echo '<img src="'.$urlImage.'" width="200" height="200"/>';

                                    do{
                                        $result = $blob_client->listBlobs($container_name, $bloblists);
                                        foreach ($result->getBlobs() as $blob)
                                        {

                                            echo '<img src="files/'.$nama.'" width="120" height="120"/>';
                                    
                                        }
                                        $bloblists->setContinuationToken($result->getContinuationToken());
                                    } while($result->getContinuationToken());

                                    $blob = $blob_client->getBlob($container_name, $filename);
                                    fpassthru($blob->getContentStream());

                                    
                                    
                                } catch(ServiceException $e){
                                    // Handle exception based on error codes and messages.
                                    // Error codes and messages are here:
                                    // http://msdn.microsoft.com/library/azure/dd179439.aspx
                                    $code = $e->getCode();
                                    $error_message = $e->getMessage();
                                    echo $code.": ".$error_message."<br />";
                                }
                                catch(InvalidArgumentTypeException $e){
                                    // Handle exception based on error codes and messages.
                                    // Error codes and messages are here:
                                    // http://msdn.microsoft.com/library/azure/dd179439.aspx
                                    $code = $e->getCode();
                                    $error_message = $e->getMessage();
                                    echo $code.": ".$error_message."<br />";
                                }
                            }else{
                                echo 'UKURAN FILE TERLALU BESAR';
                                echo "<br/>";
                                echo '<a href="index.php" class="btn btn-warning">Back</a>';
                                
                            }
                        }else{
                            echo 'EKSTENSI FILE YANG DI UPLOAD TIDAK DI PERBOLEHKAN';
                            echo "<br/>";
                            echo '<a href="index.php" class="btn btn-warning">Back</a>';
                        }


                    }
                ?>
                    
            </div>

            <div class="card-body">
                <div id="wrapper" style="width:1020px; display:table;">
                    <div id="jsonOutput" style="width:600px; display:table-cell;">
                        Response:
                        <br><br>
                        <textarea id="responseTextArea" class="UIInput"
                                style="width:580px; height:400px;"></textarea>
                    </div>
                    <div id="imageDiv" style="width:420px; display:table-cell;">
                        Source image:
                        <br><br>
                        <img id="sourceImage" width="400" />
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>