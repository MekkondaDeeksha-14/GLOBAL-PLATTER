<?php
session_start();
include 'db.php';
$user_id = $_SESSION['user_id'] ?? 1; // fallback for testing
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meme Generator | Global Platter</title>
<style>
/* ===== GLOBAL ===== */
body, html { margin:0; padding:0; font-family:"Georgia", serif; background:#fff8e7; color:#2d2d2d; }
a{text-decoration:none;}
button, input{outline:none;}

/* ===== NAVBAR ===== */
.navbar {
    position:fixed; top:0; width:100%; background:#6b0000; padding:15px 30px; display:flex; justify-content:center; align-items:center; box-shadow:0 5px 15px rgba(0,0,0,0.3); z-index:100;
}
.logo{color:#d4af37; font-size:2rem; font-weight:bold; letter-spacing:1px;}
.nav-links{display:flex; list-style:none; margin:0; padding:0; gap:20px;}
.nav-links li{display:inline-block;}
.nav-links a{color:#fff; font-weight:600; padding:6px 12px;}
.nav-links a:hover, .nav-links a.active{color:#ffd700;}

/* ===== CONTAINER ===== */
.container{max-width:950px; margin:130px auto 50px auto; padding:30px; background:#fff8e7; border:3px solid #6b0000; border-radius:18px; text-align:center; box-shadow:0 10px 30px rgba(0,0,0,0.2);}
.container:hover{box-shadow:0 12px 40px rgba(0,0,0,0.25);}
h2{font-size:2.4rem; color:#6b0000; border-bottom:2px solid #d4af37; display:inline-block; padding-bottom:8px; margin-bottom:25px;}
#dropArea{border:3px dashed #6b0000; border-radius:12px; padding:30px; cursor:pointer; margin-bottom:20px; transition:0.25s;}
#dropArea.hover{background:#ffe8c1;}
#dropArea p{font-size:18px; color:#6b0000; margin:0;}
input[type="text"]{padding:12px; margin:8px 0; width:80%; max-width:400px; border:2px solid #6b0000; border-radius:8px; font-size:16px;}
button{background:#6b0000; color:white; border:none; padding:12px 20px; border-radius:8px; cursor:pointer; margin:5px; font-size:16px; font-weight:bold; transition:0.25s;}
button:hover{background:#a30000; transform:scale(1.05);}
.emoji-bar{margin:20px 0;}
.emoji-bar span{font-size:32px; cursor:pointer; margin:6px; transition:transform 0.2s;}
.emoji-bar span:hover{transform:scale(1.4);}
canvas{margin-top:20px; border:3px solid #6b0000; border-radius:12px; max-width:100%; cursor:grab; box-shadow:0 12px 25px rgba(0,0,0,0.25);}
.download-btn{display:none; background:#2d6a4f; padding:12px 20px; color:white; border-radius:8px; margin-top:12px;}
@media(max-width:600px){input[type="text"]{width:95%;} .emoji-bar span{font-size:28px;}}
</style>
</head>
<body>

<header>
<nav class="navbar">
  <div class="logo"><a href="LandingPage.php" style="color:inherit;">GLOBAL PLATTER</a></div>
  <ul class="nav-links">
 <li><a href="login.php">Login/Signup</a></li>
    <li><a href="profile.php">My Profile</a></li>
    <li><a href="meme_generator.php" class="active">Make a Meme</a></li>
    <li><a href="Chefs.php">Chefs</a></li>
    <li><a href="about.php">About Us</a></li>
  </ul>
</nav>
</header>

<div class="container">
<h2>🎨 Creative Meme Generator</h2>

<div id="dropArea"><p>Click or drag & drop an image here</p>
<input type="file" id="imageUpload" style="display:none;">
</div>

<input type="text" id="topText" placeholder="Top Text"><br>
<input type="text" id="bottomText" placeholder="Bottom Text"><br>

<button onclick="generateAICaption()">🤖 AI Caption</button>
<button onclick="drawMeme()">Generate Meme</button>

<div class="emoji-bar">
<span onclick="addEmoji('🔥')">🔥</span>
<span onclick="addEmoji('😂')">😂</span>
<span onclick="addEmoji('🍕')">🍕</span>
<span onclick="addEmoji('🌶️')">🌶️</span>
<span onclick="addEmoji('👨‍🍳')">👨‍🍳</span>
</div>

<canvas id="memeCanvas"></canvas>
<br>
<button onclick="saveMeme()">💾 Save to Profile</button>
<a id="downloadLink" class="download-btn" download="meme.png">⬇ Download</a>
</div>

<script>
const dropArea=document.getElementById('dropArea');
const fileInput=document.getElementById('imageUpload');
const canvas=document.getElementById("memeCanvas");
const ctx=canvas.getContext("2d");
const topText=document.getElementById("topText");
const bottomText=document.getElementById("bottomText");
let image=new Image();
let emojis=[], draggingEmoji=null;

// ===== UPLOAD HANDLERS =====
dropArea.addEventListener('click',()=>fileInput.click());

fileInput.addEventListener('change', e=>{
    const file = e.target.files[0];
    if(!file) return;
    dropArea.querySelector("p").textContent = file.name;
    const reader = new FileReader();
    reader.onload = ev => { image.src = ev.target.result; };
    reader.readAsDataURL(file);
});

dropArea.addEventListener('dragover', e=>{
    e.preventDefault(); dropArea.classList.add('hover');
});
dropArea.addEventListener('dragleave', e=>{
    dropArea.classList.remove('hover');
});
dropArea.addEventListener('drop', e=>{
    e.preventDefault(); dropArea.classList.remove('hover');
    const file = e.dataTransfer.files[0];
    if(!file) return;
    dropArea.querySelector("p").textContent = file.name;
    const reader = new FileReader();
    reader.onload = ev => { image.src = ev.target.result; };
    reader.readAsDataURL(file);
});

// ===== IMAGE LOAD =====
image.onload = () => {
    const maxWidth = 800;
    const maxHeight = 600;
    let ratio = Math.min(maxWidth / image.width, maxHeight / image.height, 1);
    canvas.width = image.width * ratio;
    canvas.height = image.height * ratio;
    drawMeme();
};

// ===== AUTO FONT SIZE =====
function autoFont(text){
    let size = canvas.width / 8;
    ctx.font = `bold ${size}px Impact`;
    while(ctx.measureText(text).width > canvas.width - 20){
        size -= 2;
        ctx.font = `bold ${size}px Impact`;
    }
    return size;
}

// ===== DRAW MEME =====
function drawMeme(){
    if(!image.src) return;
    ctx.clearRect(0,0,canvas.width,canvas.height);
    ctx.drawImage(image,0,0,canvas.width,canvas.height);
    ctx.textAlign="center"; ctx.fillStyle="white"; ctx.strokeStyle="black"; ctx.lineWidth=4;
    
    let size = autoFont(topText.value.toUpperCase());
    ctx.font = `bold ${size}px Impact`;
    ctx.fillText(topText.value.toUpperCase(), canvas.width/2, 50);
    ctx.strokeText(topText.value.toUpperCase(), canvas.width/2, 50);
    
    size = autoFont(bottomText.value.toUpperCase());
    ctx.font = `bold ${size}px Impact`;
    ctx.fillText(bottomText.value.toUpperCase(), canvas.width/2, canvas.height-20);
    ctx.strokeText(bottomText.value.toUpperCase(), canvas.width/2, canvas.height-20);
    
    emojis.forEach(e=>{
        ctx.font="40px serif";
        ctx.fillText(e.char, e.x, e.y);
    });

    document.getElementById("downloadLink").href = canvas.toDataURL("image/png");
    document.getElementById("downloadLink").style.display = "inline-block";
}

// ===== EMOJI =====
function addEmoji(char){
    emojis.push({char, x:canvas.width/2, y:canvas.height/2});
    drawMeme();
}

canvas.addEventListener("mousedown", e=>{
    emojis.forEach(em=>{
        if(Math.abs(e.offsetX-em.x)<30 && Math.abs(e.offsetY-em.y)<30){
            draggingEmoji=em;
        }
    });
});
canvas.addEventListener("mousemove", e=>{
    if(draggingEmoji){
        draggingEmoji.x=e.offsetX;
        draggingEmoji.y=e.offsetY;
        drawMeme();
    }
});
canvas.addEventListener("mouseup", ()=>draggingEmoji=null);
canvas.addEventListener("mouseleave", ()=>draggingEmoji=null);

// ===== AI CAPTION =====
function generateAICaption(){
    const captions=[
        ["WHEN THE FOOD HITS DIFFERENT","CHEF UNDERSTOOD THE ASSIGNMENT"],
        ["EXPECTATION VS REALITY","STILL ATE IT THOUGH"],
        ["SPICY LEVEL: DANGEROUS","NO REGRETS"],
        ["ME AFTER FIRST BITE","PURE HAPPINESS"],
        ["WHEN MOM SEES YOU COOKING","MASTER CHEF MODE"]
    ];
    const pick = captions[Math.floor(Math.random()*captions.length)];
    topText.value = pick[0]; bottomText.value = pick[1];
    drawMeme();
}

// ===== SAVE MEME =====
function saveMeme(){
    if(!image.src) { alert("Please upload an image first!"); return; }
    const imageData=canvas.toDataURL("image/png");
    fetch("save_meme.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"image="+encodeURIComponent(imageData)
    }).then(res=>res.text()).then(data=>{
        if(data.trim()==="success"){alert("✅ Meme saved to your profile!");}
        else{alert("❌ Failed to save meme");}
    }).catch(()=>alert("Server error"));
}
</script>
</body>
</html>
