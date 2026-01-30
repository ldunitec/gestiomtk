
  <!DOCTYPE html>
 
  <html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comandos</title>
</head>

<body>
    <div class="card">
        <img class="card-img-top" src="holder.js/100x180/" alt="Title" />
        <div class="card-body">
            <h4 class="card-title">Permisos</h4>
            <p class="card-text">
            <h3>Cuando se crea una nueva ruta:</h3>
            <h4 style="color: red; margin-left: 15px;">
                <ul>
                    <li><b>php artisan tinker</b></li>
                    <li><b>$user = App\Models\User::find(1);</b> // Busca un usuario: Reemplaza con el ID real</li>
                    <li><b>$user->assignRole('admin');</b> // Asignar un rol existente: Reemplaza "admin" con el nombre
                        real del rol</li>
                    <li><b>$user->hasRole('admin');</b> // Verificar si el usuario tiene ese rol: devuelve true o false
                    </li>
                    <li><b>$user->getRoleNames();</b> // Ver roles del usuario:devuelve una colección con los nombres de
                        los roles</li>
                    <li>si el permiso no existe:</li>
                    <li><b>Spatie\Permission\Models\Permission::create(['name' => 'ver reportes']);</b></li>
                </ul>

                <h2>Crear el permiso y asignarlo al rol administrador</h2>
                <textarea name="" id="" style="width: 100%;">
✅ Paso 1: Crear el permiso

Abre Tinker:

php artisan tinker

Crea el permiso:

Spatie\Permission\Models\Permission::create(['name' => 'ver reportes']);

✅ Paso 2: Asignar ese permiso al rol "admin" (o "administrador")

$role = Spatie\Permission\Models\Role::findByName('admin');
$role->givePermissionTo('ver reportes');

✅ Paso 3: Verifica que el usuario con ese rol ahora pueda acceder

Tu middleware:

'middleware' => ['permission:ver reportes']

verificará que el usuario autenticado tenga ese permiso (directamente o por rol).</textarea>
            </h4>
            </p>

<div class="mb-3">
    <label for="" class="form-label"></label>
    <h2>Asignarle el permiso al usuario administrador</h2>
     <textarea name="" id="" style="width: 100%;">     
✅ 1. Abre Tinker

php artisan tinker

✅ 2. Busca un usuario

$user = App\Models\User::find(1); // Reemplaza con el ID real

✅ 3. Asignar un rol existente

$user->assignRole('admin'); // Reemplaza "admin" con el nombre real del rol

    Esto funcionará siempre y cuando el rol ya exista en tu base de datos.

✅ 4. Si el rol aún no existe, créalo:

Spatie\Permission\Models\Role::create(['name' => 'admin']);

Y luego asígnalo:

$user->assignRole('admin');

✅ 5. Verificar si el usuario tiene ese rol

$user->hasRole('admin'); // devuelve true o false

✅ 6. Ver roles del usuario

$user->getRoleNames(); // devuelve una colección con los nombres de los roles
    </textarea>
</div>
            
        </div>
    </div>

</body>

</html>






