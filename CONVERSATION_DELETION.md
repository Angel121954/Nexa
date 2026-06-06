# Eliminación de Conversación

## Flujo completo

1. **Usuario A** da corazón al **Usuario B**
2. **Usuario B** recibe notificación del like
3. Si **Usuario B** devuelve el corazón, se crea el match y la conversación
4. Si **Usuario A** elimina la conversación:
   - Se marca `user1_deleted_at` en la tabla `matches`
   - **Usuario A** ya no ve la conversación en su lista
   - **Usuario B** sigue viendo la conversación con normalidad
5. Si **Usuario B** envía un nuevo mensaje:
   - Se limpian ambos `user1_deleted_at` y `user2_deleted_at`
   - **Usuario A** vuelve a visualizar la conversación completa
6. Si **Usuario B** también elimina la conversación:
   - Se marca `user2_deleted_at` en la tabla `matches`
   - Ambos usuarios ocultan la conversación
   - El match sigue existiendo en la base de datos
7. Si ambos eliminaron y uno da like nuevamente (match mutuo):
   - Se restauran ambos flags (`deleted_at = null`)
   - La conversación reaparece para los dos

## Estructura de la tabla `matches`

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | bigint | ID único |
| `user1_id` | bigint | Primer usuario |
| `user2_id` | bigint | Segundo usuario |
| `user1_deleted_at` | timestamp nullable | Marca si user1 eliminó la conversación |
| `user2_deleted_at` | timestamp nullable | Marca si user2 eliminó la conversación |
| `created_at` | timestamp | Fecha de creación del match |
| `updated_at` | timestamp | Fecha de última actualización |

## Reglas de negocio

- El borrado es **por usuario**: cada quien controla su visibilidad
- No se eliminan registros físicamente ni se pierden mensajes
- El match permanece en la base de datos aunque ambos eliminen
- Enviar un mensaje restaura la conversación para ambos (limpia ambos `deleted_at`)
- Dar like mutuo cuando el match ya existe también restaura ambos lados
- No se emite broadcast `MatchDeleted` al marcar el borrado (el otro usuario no debe recibir notificación)

## Archivos involucrados

- `app/Models/UserMatch.php` — casts, `$fillable`, helper `isDeletedByUser()`
- `app/Http/Controllers/Api/MatchController.php` — `destroy()` marca el lado del usuario
- `app/Http/Controllers/Api/MessageController.php` — `store()` limpia ambos flags al enviar mensaje
- `app/Http/Controllers/ExploreController.php` — `like()` restaura ambos flags en match mutuo existente
- `app/Http/Controllers/MessagePageController.php` — `index()` filtra matches no eliminados por el usuario
- `database/migrations/*_add_user_deleted_at_to_matches_table.php` — migración de columnas
- `public/js/messages/interactions.js` — `DeleteUI` elimina del DOM y auto-selecciona siguiente conversación
- `public/js/messages/chat.js` — ya no escucha `MatchDeleted`
