-- Script para agregar columna 'activo' a las tablas existentes
-- Ejecutar en phpMyAdmin o cliente MySQL

-- Agregar columna activo a tabla calificaciones
ALTER TABLE calificaciones ADD COLUMN activo TINYINT(1) DEFAULT 1;

-- Agregar columna activo a tabla contactos  
ALTER TABLE contactos ADD COLUMN activo TINYINT(1) DEFAULT 1;

-- Actualizar registros existentes para que tengan activo = 1
UPDATE calificaciones SET activo = 1 WHERE activo IS NULL;
UPDATE contactos SET activo = 1 WHERE activo IS NULL;
