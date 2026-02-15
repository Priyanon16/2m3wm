<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Kanit',sans-serif;
    background:#f4f6f9;
    margin:0;
}

/* Layout */
.layout{
    display:flex;
    min-height:100vh;
}

/* Sidebar */
.sidebar{
    width:260px;
    background:#111;
    color:#fff;
    transition:.3s;
}

.sidebar.collapsed{
    width:80px;
}

.sidebar .nav-link{
    color:#fff;
    display:flex;
    align-items:center;
    gap:10px;
    padding:10px 15px;
}

.sidebar .nav-link:hover{
    background:rgba(255,255,255,0.1);
}

.brand-accent{
    color:#ff7a00;
}

/* Main content */
.main-content{
    flex:1;
    padding:30px;
}
</style>
