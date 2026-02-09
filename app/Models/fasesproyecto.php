Fase 1: El MVP (Lo que necesitas entregar YA)

Este es el flujo crítico. Sin esto, el sistema no tiene razón de ser.

    Gestión de Inventario (Básico):

        CRUD de Productos (Crear, leer, actualizar).

        Registro manual de Tipos de Movimiento (Entrada/Salida).

    Ventas Directas (El corazón):

        Registro de una Venta vinculada a un Cliente y una Forma de Pago.

        Inserción de Detalles de Venta (Renglones).

        Lógica automática: Restar la cantidad vendida de la existencia en la tabla productos.

    Seguridad Básica:

        Login funcional vinculado a la tabla de Empleados (para saber quién realizó la operación).

Fase 2: Siguiente paso (Operatividad y Compras)

Una vez que el prototipo venda, lo siguiente es asegurar el reabastecimiento y el control administrativo.

    Módulo de Compras:

        Registro de entrada de mercancía mediante Acreedores.

        Lógica automática: Sumar la existencia al inventario y actualizar el costo_unitario.

    Historial de Inventario (Kardex):

        Pantalla para ver los Movimientos de Inventario (saber por qué subió o bajó el stock).

    Dashboard de Reportes:

        Gráficas simples de "Productos más vendidos" y "Total de ventas por día".

Fase 3: Finalización (Finanzas y Detalles)

Aquí es donde el proyecto pasa de ser un "registro" a un "sistema de gestión".

    Cuentas por Cobrar y Pagar:

        Generar la deuda si la venta/compra no es de contado.

        Registro de abonos (Nivel 5).

    Módulo de Nómina:

        Generación de recibos de pago basados en el Cargo y los días trabajados.

        Cálculo de deducciones (IVSS, FAOV).

    Refinamiento:

        Validaciones de formularios (que no falten campos).

        Exportación de reportes a PDF.