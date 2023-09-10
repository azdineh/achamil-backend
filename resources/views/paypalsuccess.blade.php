<html>
  <head>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
    <script>
        function goBack() {
            window.history.back();
            //window.location.href = '/';
            
        }
    </script>
  </head>
    <style>
      body {
        text-align: center;
        padding: 40px 0;
        background: #EBF0F5;
      }
        h1 {
          color: #88B04B;
          font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
          font-weight: 900;
          font-size: 40px;
          margin-bottom: 10px;
        }
        p {
          color: #404F5E;
          font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
          font-size:20px;
          margin: 0;
        }
      i {
        color: #9ABC66;
        font-size: 100px;
        line-height: 200px;
        margin-left:-15px;
      }
      .card {
        background: white;
        padding: 60px;
        border-radius: 4px;
        box-shadow: 0 2px 3px #C8D0D8;
        display: block;
        margin: 0 auto;
      }
      .myButton {
	background-color:#44c767;
	border-radius:24px;
	border:2px solid #18ab29;
	display:inline-block;
	cursor:pointer;
	color:#ffffff;
	font-family:Arial;
	font-size:17px;
	padding:14px 76px;
	text-decoration:none;
	text-shadow:0px 0px 0px #2f6627;
}
.myButton:hover {
	background-color:#5cbf2a;
}
.myButton:active {
	position:relative;
	top:1px;
}

    </style>
    <body>
      <div class="card">
      <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">
        <i class="checkmark">✓</i>
      </div>
        <h1>Success</h1> 
        <p style="font-size:26px">تمت عملية الأداء بنجاح<br/> </p>
        <p> <br/><br/>
        <p>إضغط على الزر -الصفحة الرئيسية- للعودة الى التطبيق</p>

        <!-- <button onclick="goBack()" class="myButton">الرجوع الى التطبيق</button> -->

    
      </div>
    </body>
</html>
