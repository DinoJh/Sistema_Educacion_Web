<div class="text-center py-5">
    <div style="margin-bottom:1.5rem;">
        <img src="<?= base_url('public/img/logo-codePuno.gif') ?>" alt="CodePuno" style="width:110px;height:auto;filter:drop-shadow(0 8px 24px rgba(124,58,237,.4));">
    </div>
    <h1 class="fw-bold mb-2" style="font-size:2.8rem;background:linear-gradient(135deg,#a78bfa,#06b6d4);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">CodePuno</h1>
    <p class="mb-1" style="font-size:1.15rem;color:var(--cl-muted);">Plataforma de cursos de programación</p>
    <p class="text-cl-muted small mb-5">Bienvenido, <strong style="color:var(--cl-accent2);"><?= ucwords(strtolower($session->datos ?? '')) ?></strong></p>

    <div class="row justify-content-center g-3">
    <?php if(isset($session) && $session->perf_ide == 1): // ALUMNO ?>
        <div class="col-md-4 col-sm-6">
            <div class="stat-card text-center" style="cursor:pointer;transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(124,58,237,.25)'" onmouseout="this.style.transform='';this.style.boxShadow=''" onclick="cargarFuncion('/mi-panel/cursos','Mi Panel','Mis Cursos','Cursos en los que estás inscrito')">
                <div style="width:52px;height:52px;border-radius:14px;background:rgba(124,58,237,.15);display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 12px;">📚</div>
                <h6 class="fw-bold mb-1">Mis Cursos</h6>
                <p class="text-cl-muted small mb-0">Cursos en los que estás inscrito</p>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="stat-card text-center" style="cursor:pointer;transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(6,182,212,.25)'" onmouseout="this.style.transform='';this.style.boxShadow=''" onclick="cargarFuncion('/mi-panel/catalogo','Mi Panel','Buscar Cursos','Explora todos los cursos disponibles')">
                <div style="width:52px;height:52px;border-radius:14px;background:rgba(6,182,212,.15);display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 12px;">🔍</div>
                <h6 class="fw-bold mb-1">Buscar Cursos</h6>
                <p class="text-cl-muted small mb-0">Explora y únete a nuevos cursos</p>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="stat-card text-center" style="cursor:pointer;transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(16,185,129,.25)'" onmouseout="this.style.transform='';this.style.boxShadow=''" onclick="cargarFuncion('/mi-panel/progreso','Mi Panel','Mi Progreso','Tu avance en los cursos')">
                <div style="width:52px;height:52px;border-radius:14px;background:rgba(16,185,129,.15);display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 12px;">📈</div>
                <h6 class="fw-bold mb-1">Mi Progreso</h6>
                <p class="text-cl-muted small mb-0">Revisa tu avance en los cursos</p>
            </div>
        </div>
    <?php elseif(isset($session) && $session->perf_ide == 2): // PROFESOR ?>
        <div class="col-md-4 col-sm-6">
            <div class="stat-card text-center" style="cursor:pointer;transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(124,58,237,.25)'" onmouseout="this.style.transform='';this.style.boxShadow=''" onclick="cargarFuncion('/cursos','Cursos','Mis Cursos','Gestiona tus cursos y lecciones')">
                <div style="width:52px;height:52px;border-radius:14px;background:rgba(124,58,237,.15);display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 12px;">✏️</div>
                <h6 class="fw-bold mb-1">Mis Cursos</h6>
                <p class="text-cl-muted small mb-0">Crea y gestiona tus cursos</p>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="stat-card text-center" style="cursor:pointer;transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(6,182,212,.25)'" onmouseout="this.style.transform='';this.style.boxShadow=''" onclick="cargarFuncion('/usuarios/misalumnos','Usuarios','Mis Alumnos','Alumnos inscritos en tus cursos')">
                <div style="width:52px;height:52px;border-radius:14px;background:rgba(6,182,212,.15);display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 12px;">👥</div>
                <h6 class="fw-bold mb-1">Mis Alumnos</h6>
                <p class="text-cl-muted small mb-0">Ve quiénes están en tus cursos</p>
            </div>
        </div>
    <?php else: // ADMIN ?>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card text-center" style="cursor:pointer;transition:transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''" onclick="cargarFuncion('/usuarios/alumnos','Usuarios','Alumnos','')">
                <div style="width:52px;height:52px;border-radius:14px;background:rgba(124,58,237,.15);display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 12px;">👥</div>
                <h6 class="fw-bold mb-1">Alumnos</h6><p class="text-cl-muted small mb-0">Gestionar alumnos</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card text-center" style="cursor:pointer;transition:transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''" onclick="cargarFuncion('/usuarios/profesores','Usuarios','Profesores','')">
                <div style="width:52px;height:52px;border-radius:14px;background:rgba(6,182,212,.15);display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 12px;">🎓</div>
                <h6 class="fw-bold mb-1">Profesores</h6><p class="text-cl-muted small mb-0">Gestionar profesores</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card text-center" style="cursor:pointer;transition:transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''" onclick="cargarFuncion('/cursos','Cursos','Todos los Cursos','')">
                <div style="width:52px;height:52px;border-radius:14px;background:rgba(16,185,129,.15);display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 12px;">📚</div>
                <h6 class="fw-bold mb-1">Cursos</h6><p class="text-cl-muted small mb-0">Ver y gestionar cursos</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card text-center" style="cursor:pointer;transition:transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''" onclick="cargarFuncion('/reportes/progreso','Reportes','Estadísticas','')">
                <div style="width:52px;height:52px;border-radius:14px;background:rgba(245,158,11,.15);display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 12px;">📊</div>
                <h6 class="fw-bold mb-1">Reportes</h6><p class="text-cl-muted small mb-0">Estadísticas de la plataforma</p>
            </div>
        </div>
    <?php endif; ?>
    </div>
</div>
