-- --------------------------------------------------
-- 1. Tabla de Roles
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50) NOT NULL UNIQUE,
  `descripcion` VARCHAR(255),
  `creado_at` DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------
-- 2. Tabla de Usuarios
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre`        VARCHAR(100) NOT NULL,
  `email`         VARCHAR(150) NOT NULL UNIQUE,
  `telefono`      VARCHAR(20),
  `password_hash` VARCHAR(255) NOT NULL,
  `role_id`       INT NOT NULL,
  `activo`        TINYINT(1) NOT NULL DEFAULT 1,
  `creado_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------
-- 3. Tabla de Pacientes
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS `pacientes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre`          VARCHAR(100) NOT NULL,
  `apellidos`       VARCHAR(150),
  `fecha_nacimiento` DATE,
  `genero`          ENUM('M','F','O'),
  `telefono`        VARCHAR(20),
  `email`           VARCHAR(150),
  `direccion`       TEXT,
  `creado_at`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------
-- 4. Tabla de Expedientes
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS `expedientes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `paciente_id`     INT NOT NULL,
  `creado_por`      INT NOT NULL,
  `fecha_apertura`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `motivo_apertura` VARCHAR(255),
  `estado`          ENUM('Activo','Cerrado') NOT NULL DEFAULT 'Activo',
  FOREIGN KEY (`paciente_id`) REFERENCES `pacientes`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`creado_por`)  REFERENCES `usuarios`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------
-- 5. Tabla de Consultas
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS `consultas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `expediente_id`  INT NOT NULL,
  `medico_id`      INT NOT NULL,
  `fecha_consulta` DATETIME NOT NULL,
  `motivo`         TEXT,
  `diagnostico`    TEXT,
  `tratamiento`    TEXT,
  `notas`          TEXT,
  `creado_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`expediente_id`) REFERENCES `expedientes`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`medico_id`)      REFERENCES `usuarios`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------
-- 6. Tabla de Horarios (disponibilidad semanal de médicos)
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS `horarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `medico_id`  INT NOT NULL,
  `dia_semana` TINYINT  NOT NULL COMMENT '0=Domingo,6=Sábado',
  `hora_inicio` TIME    NOT NULL,
  `hora_fin`    TIME    NOT NULL,
  UNIQUE KEY `uq_medico_dia_hora` (`medico_id`,`dia_semana`,`hora_inicio`),
  FOREIGN KEY (`medico_id`) REFERENCES `usuarios`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------
-- 7. Tabla de Citas / Agenda
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS `citas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `paciente_id`   INT NOT NULL,
  `medico_id`     INT NOT NULL,
  `fecha_inicio`  DATETIME NOT NULL,
  `fecha_fin`     DATETIME NOT NULL,
  `estado`        ENUM('Pendiente','Confirmada','Cancelada','Realizada') NOT NULL DEFAULT 'Pendiente',
  `creado_por`    INT NOT NULL COMMENT 'Usuario que programa la cita',
  `creado_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_medico_fecha` (`medico_id`,`fecha_inicio`),
  FOREIGN KEY (`paciente_id`) REFERENCES `pacientes`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`medico_id`)   REFERENCES `usuarios`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`creado_por`)  REFERENCES `usuarios`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
