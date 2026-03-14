<div class="text-center py-5">
    <div style="font-size:4rem;margin-bottom:1rem;">💻</div>
    <h3 class="fw-bold mb-2">Bienvenido a <span class="text-accent">CodeLearn</span></h3>
    <p class="text-cl-muted mb-4">Plataforma de cursos de programación</p>
    <div class="row justify-content-center g-3 mt-2">
        <?php if(isset($session) && $session->perf_ide==1): ?>
        <div class="col-md-4"><div class="stat-card text-center" style="cursor:pointer;" onclick="cargarFuncion('/mi-panel/cursos','Mi Panel','Explorar Cursos','')">
            <i class="ti-book fs-2 mb-2" style="color:var(--cl-accent2);"></i>
            <h6 class="fw-bold">Explorar Cursos</h6><p class="text-cl-muted small mb-0">Descubre todos los cursos disponibles</p>
        </div></div>
        <div class="col-md-4"><div class="stat-card text-center" style="cursor:pointer;" onclick="cargarFuncion('/mi-panel/progreso','Mi Panel','Mi Progreso','')">
            <i class="ti-stats-up fs-2 mb-2" style="color:#10b981;"></i>
            <h6 class="fw-bold">Mi Progreso</h6><p class="text-cl-muted small mb-0">Revisa tu avance en los cursos</p>
        </div></div>
        <?php elseif(isset($session) && $session->perf_ide==2): ?>
        <div class="col-md-4"><div class="stat-card text-center" style="cursor:pointer;" onclick="cargarFuncion('/cursos','Cursos','Mis Cursos','')">
            <i class="ti-pencil fs-2 mb-2" style="color:var(--cl-accent2);"></i>
            <h6 class="fw-bold">Mis Cursos</h6><p class="text-cl-muted small mb-0">Gestiona tus cursos y lecciones</p>
        </div></div>
        <?php else: ?>
        <div class="col-md-3"><div class="stat-card text-center" style="cursor:pointer;" onclick="cargarFuncion('/usuarios/alumnos','Usuarios','Alumnos','')">
            <i class="ti-user fs-2 mb-2" style="color:var(--cl-accent2);"></i>
            <h6 class="fw-bold">Alumnos</h6><p class="text-cl-muted small mb-0">Gestionar alumnos</p>
        </div></div>
        <div class="col-md-3"><div class="stat-card text-center" style="cursor:pointer;" onclick="cargarFuncion('/cursos','Cursos','Todos los Cursos','')">
            <i class="ti-book fs-2 mb-2" style="color:#10b981;"></i>
            <h6 class="fw-bold">Cursos</h6><p class="text-cl-muted small mb-0">Ver todos los cursos</p>
        </div></div>
        <div class="col-md-3"><div class="stat-card text-center" style="cursor:pointer;" onclick="cargarFuncion('/reportes/progreso','Reportes','Progreso','')">
            <i class="ti-bar-chart fs-2 mb-2" style="color:#f59e0b;"></i>
            <h6 class="fw-bold">Reportes</h6><p class="text-cl-muted small mb-0">Estadísticas de la plataforma</p>
        </div></div>
        <?php endif; ?>
    </div>
</div>
