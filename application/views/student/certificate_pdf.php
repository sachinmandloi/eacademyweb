<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <!--<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,900&display=swap" rel="stylesheet">
    <style>
body {
    background: #f7f7fb;
    font-weight: 400;
    font-size: 16px;
    line-height: 26px;
    color: #888888;
    font-family: 'Poppins', sans-serif;
    transform:scale(1);
    }
.edu_certifiate_heading {
    padding: 140px 0 0 181px;
    }
.edu_certifiate_wrapper {
    background: url("data:image/jpg;base64,<?php echo base64_encode(file_get_contents(base_url().'assets/images/certificate_bg.png'));?>");
    width: 1203px;
    height: 871px;
    margin: auto;
    position: relative;
    
}
.title1 {
    font-size: 80px;
    text-transform: uppercase;
    color: #4d4a81;
    margin-bottom: 37px;
}
.title2 {
    font-size: 30px;
    font-weight: 400;
    text-transform: uppercase;
    color: #f0c586;
    position: relative;
    display: inline-block;
    padding: 0;
    margin: 0;
    padding-left: 264px;
}
.certifiate_prodly_wrap {
    text-align: center;
}
.certificate_Proudly {
    font-size: 26px;
    color: #4d4784;
    font-weight: 500;
    margin-top: 40px;
}
.name_title {
    font-size: 45px;
    font-weight: 700;
    text-transform: capitalize;
    text-align: center;
    color: #242424;
    margin-bottom: 33px;
    margin-top: 50px;
}
.certificate_discrip {
    font-size: 18px;
    text-align: center;
    padding: 0 12%;
    line-height: 28px;
}
/*.certifiate_main_logo {*/

.certifiate_main_logo {
    position: absolute;
    top: 180px;
    right: 105px;
    width: 180px;
    height: 180px;
    justify-content: center;
    align-items: center;
    border-radius: 100%;
    overflow: hidden;
}
.certificate_logo {
    width: 70%;
}
.certifiate_date {
    position: absolute;
    bottom: 325px;
    left: 233px;
    color: #4d4784;
    font-weight: 500;
    font-size: 20px;
}
.certifiate_sign {
    position: absolute;
    right: 171px;
    bottom: 325px;
    width: 100px;
    height: 50px;
}
.certifiate_sign > img {
    width: 100px;
    height: 50px;
    object-fit: contain;
}

.title2:after {
    content: "";
    position: absolute;
    left: -164px;
    width: 420px;
    top: 0;
    height: 23px;
    background: #fcd195;
    bottom: 0;
    margin: auto;
}
.certificate_btn {
    height: 45px;
    line-height: 45px;
    text-align: center;
    padding: 0 15px;
    background-color: #4d4a81;
    display: inline-block;
    font-size: 14px;
    color: #ffffff;
    font-weight: 600;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    outline: none;
    min-width: 170px;
    position: relative;
    box-shadow: none;
    text-transform: uppercase;
}
.certificate_btn_wrap {
    text-align: center;
}
    </style>
  </head>
<body class="form">
    <?php if(!empty($student_certificate)){ ?>
<div class="conatiner_certificate">   
    <div class="edu_certifiate_wrapper">
        <div class="edu_certifiate_inner">
           <div class="edu_certifiate_heading">
                <h1 class="title1"><?php echo $certificate_details[0]['heading']?></h1>
                <h4 class="title2"> <?php echo $certificate_details[0]['sub_heading']?></h4>
            </div> 
            <div class="certifiate_prodly_wrap">
                <h2 class="certificate_Proudly"> <?php echo $certificate_details[0]['title']?></h2>
            </div> 
             
            <div class="certifiate_name_wrap">
                <h2 class="name_title"><?php echo $student_details[0]['name'];?> </h2>
            </div>
           <p class="certificate_discrip"><?php echo str_replace('{batch}','<b>'.$batchdata[0]['batch_name'].'</b>', $certificate_details[0]['description']);?></p>
            
        </div>
        <div class="certifiate_date">
            <p><?php echo date('d-m-Y',strtotime($student_certificate[0]['date']));?></p>
        </div>
        <div class="certifiate_sign">
            
            <?php echo ' <img src="data:image/jpg;base64,'.base64_encode(file_get_contents(base_url().'uploads/site_data/'.$certificate_details[0]['signature_image'])).'" alt="signature">';?>
           
        </div>
        
    
         <div class="certifiate_main_logo">
           
            <?php echo ' <img src="data:image/jpg;base64,'.base64_encode(file_get_contents(base_url().'uploads/site_data/'.$certificate_details[0]['certificate_logo'])).'" class="certificate_logo" alt="Logo">';?>
        </div>
    </div>
    <?php  }else{
    echo "no data available";
    
    } ?>
</body>
</html>