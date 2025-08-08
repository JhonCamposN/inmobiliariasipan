# 📊 Configuración de Base de Datos - Sipán Inmobiliaria

## 🎯 **Propósito**
Este sistema permite que las calificaciones y comentarios de los clientes se guarden en una base de datos MySQL real, en lugar de ser solo simulación.

## 🚀 **Pasos para Configurar**

### **1. Configurar MySQL en XAMPP**
1. Abre XAMPP Control Panel
2. Inicia Apache y MySQL
3. Haz clic en "Admin" de MySQL (o ve a `http://localhost/phpmyadmin`)

### **2. Importar la Base de Datos**

#### **Opción A: Importación Manual (Recomendada)**
1. En phpMyAdmin, haz clic en "Importar" en la barra superior
2. Haz clic en "Seleccionar archivo"
3. Selecciona el archivo `php/database-simple.sql`
4. Haz clic en "Continuar" para importar

#### **Opción B: Copiar y Pegar SQL**
1. En phpMyAdmin, ve a la pestaña "SQL"
2. Abre el archivo `php/database-simple.sql` en un editor de texto
3. Copia todo el contenido
4. Pégalo en la ventana SQL de phpMyAdmin
5. Haz clic en "Continuar"

### **3. Verificar la Importación**
Después de importar, deberías ver:
- Una base de datos llamada `sipan_inmobiliaria`
- Dos tablas: `calificaciones` y `estadisticas_calificaciones`
- 5 comentarios de ejemplo en la tabla `calificaciones`

### **4. Configurar Credenciales**
Edita el archivo `php/config-database.php` y actualiza estas líneas:

```php
define('DB_USER', 'root'); // Tu usuario de MySQL (normalmente 'root')
define('DB_PASS', ''); // Tu contraseña de MySQL (normalmente vacía en XAMPP)
```

### **5. Verificar la Configuración**
1. Abre `http://localhost/inmobiliaria_sipan/index.html`
2. Ve a la sección "Calificaciones"
3. Deberías ver comentarios reales cargados desde la base de datos

## 📋 **Funcionalidades Implementadas**

### **✅ Base de Datos Real**
- **Tabla `calificaciones`**: Almacena nombre, email, calificación (1-5), comentario, fecha
- **Tabla `estadisticas_calificaciones`**: Cachea estadísticas para mejor rendimiento
- **Triggers automáticos**: Actualizan estadísticas cuando se agregan nuevas calificaciones

### **✅ API REST**
- **GET `/php/calificaciones-api.php`**: Obtiene estadísticas y comentarios
- **POST `/php/calificaciones-api.php`**: Agrega nuevas calificaciones
- **Validación completa**: Verifica datos antes de guardar

### **✅ Funcionalidades JavaScript**
- **Carga automática**: Los comentarios se cargan desde la base de datos al abrir la página
- **Envío en tiempo real**: Las nuevas calificaciones se guardan inmediatamente
- **Actualización dinámica**: Las estadísticas se actualizan automáticamente

## 🔧 **Estructura de la Base de Datos**

### **Tabla `calificaciones`**
```sql
- id (INT, AUTO_INCREMENT, PRIMARY KEY)
- nombre (VARCHAR(100))
- email (VARCHAR(150))
- calificacion (INT, 1-5)
- comentario (TEXT)
- fecha_creacion (TIMESTAMP)
- estado (ENUM: 'activo', 'inactivo')
- ip_address (VARCHAR(45))
- user_agent (TEXT)
```

### **Tabla `estadisticas_calificaciones`**
```sql
- total_calificaciones (INT)
- promedio_calificacion (DECIMAL(3,2))
- porcentaje_satisfechos (DECIMAL(5,2))
- ultima_actualizacion (TIMESTAMP)
```

## 🛠️ **Solución de Problemas**

### **Error: "No se pudo conectar a la base de datos"**
1. Verifica que MySQL esté iniciado en XAMPP
2. Revisa las credenciales en `php/config-database.php`
3. Asegúrate de que la base de datos `sipan_inmobiliaria` existe

### **Error: "La tabla de calificaciones no existe"**
1. Ejecuta el archivo `php/database-simple.sql` en phpMyAdmin
2. Verifica que las tablas se crearon correctamente

### **Los comentarios no se cargan**
1. Abre la consola del navegador (F12)
2. Revisa si hay errores en la pestaña "Console"
3. Verifica que el archivo `php/calificaciones-api.php` sea accesible

### **Problemas con la importación**
- **Usa `database-simple.sql`**: Es más compatible con phpMyAdmin
- **Verifica la codificación**: Asegúrate de que el archivo esté en UTF-8
- **Revisa los permisos**: Asegúrate de que MySQL tenga permisos para crear bases de datos

## 📊 **Datos de Ejemplo**
El archivo `database-simple.sql` incluye 5 comentarios de ejemplo para que puedas ver cómo funciona el sistema inmediatamente.

## 🔒 **Seguridad**
- **Validación de entrada**: Todos los datos se validan antes de guardar
- **Limpieza de datos**: Se previenen ataques XSS
- **Prepared Statements**: Se previenen inyecciones SQL
- **Logs de auditoría**: Se registran IP y User Agent

## 🎉 **¡Listo!**
Una vez configurado, el sistema de calificaciones funcionará completamente con base de datos real, permitiendo:
- Guardar calificaciones permanentes
- Mostrar estadísticas reales
- Cargar comentarios desde la base de datos
- Actualizar automáticamente las estadísticas 