@extends('adminlte::page')

@section('content_header')
    <h1><b>Listado de equipos </b></h1>
    <hr>
@stop

@section('content')
    {{-- @include('admin.equipos.parciales.modal'); --}}

    <!-- Mikrotik-->
    <div class="row">
      <div class="col-12">

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Mikrotik</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearEquipoModal">
                            Asignar puerto
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    {{-- MIKROTIK R-01 --}}
                    <div class="row">
                 <div class="col-12">

                            <div class="row">
                                <div class="table-responsive">
                                    <table
                                        class="table table-striped table-hover table-borderless table-primary align-middle">
                                        <thead class="table-light">
                                            <caption>
                                                MIKROTIK R-01
                                            </caption>
                                            <tr>
                                                <th>1</th>
                                                <th>2</th>
                                                <th>3</th>
                                                <th>4</th>
                                                <th>5</th>
                                                <th>6</th>
                                                <th>7</th>
                                                <th>8</th>
                                                <th>9</th>
                                                <th>10</th>
                                                <th>11</th>
                                                <th>12</th>
                                                <th>13</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            <tr class="table-primary">
                                                <td id="A"> PP-1</td>
                                                <td id="A"> PP-2</td>
                                                <td id="A"> PP-3</td>
                                                <td id="A"> PP-4</td>
                                                <td id="I"> PP-5</td>
                                                <td id="I"> PP-6</td>
                                                <td id="I"> PP-7</td>
                                                <td id="I"> PP-8</td>
                                                <td id="I"> PP-9</td>
                                                <td id="I"> PP-10</td>
                                                <td id="I"> PP-11</td>
                                                <td id="I"> PP-12</td>
                                                <td id="I"> PP-13</td>
                                            </tr>

                                        </tbody>

                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Patch Panel A       --}}
    <div class="row">
       <div class="col-12">

            <div class="card card-outline card-primary" >
                <div class="card-header">
                    <h3 class="card-title">PP-01</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearEquipoModal">
                            Asignar puerto
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>

                <div class="row">
                   <div class="col-12">

                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-borderless table-primary align-middle">
                                    <thead class="table-light">
                                        <caption>
                                            Patch Panel A
                                        </caption>
                                        <tr>
                                            <th>1</th>
                                            <th>2</th>
                                            <th>3</th>
                                            <th>4</th>
                                            <th>5</th>
                                            <th>6</th>
                                            <th>7</th>
                                            <th>8</th>
                                            <th>9</th>
                                            <th>10</th>
                                            <th>11</th>
                                            <th>12</th>
                                            <th>13</th>
                                            <th>14</th>
                                            <th>15</th>
                                            <th>16</th>
                                            <th>17</th>
                                            <th>18</th>
                                            <th>19</th>
                                            <th>20</th>
                                            <th>21</th>
                                            <th>22</th>
                                            <th>23</th>
                                            <th>24</th>

                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <tr class="table-primary">
                                            <td id="I"> PP-1</td>
                                            <td id="I"> PP-2</td>
                                            <td id="A"> PP-3</td>
                                            <td id="I"> PP-4</td>
                                            <td id="I"> PP-5</td>
                                            <td id="I"> PP-6</td>
                                            <td id="I"> PP-7</td>
                                            <td id="I"> PP-8</td>
                                            <td id="I"> PP-9</td>
                                            <td id="I"> PP-10</td>
                                            <td id="I"> PP-11</td>
                                            <td id="I"> PP-12</td>
                                            <td id="I"> PP-13</td>
                                            <td id="I"> PP-14</td>
                                            <td id="I"> PP-15</td>
                                            <td id="I"> PP-16</td>
                                            <td id="I"> PP-17</td>
                                            <td id="I"> PP-18</td>
                                            <td id="I"> PP-19</td>
                                            <td id="I"> PP-20</td>
                                            <td id="I"> PP-21</td>
                                            <td id="I"> PP-22</td>
                                            <td id="I"> PP-23</td>
                                            <td id="I"> PP-24</td>

                                        </tr>

                                    </tbody>

                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
   
    <!-- SW-01  cisco-->
    <div class="row">
       <div class="col-12">

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">SW-01 CISCO</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearEquipoModal">
                            Asignar puerto
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <div class="card-body">
                    <div class="row">
                       <div class="col-12">

                            <div class="row">
                                <div class="table-responsive">
                                    <table
                                        class="table table-striped table-hover table-borderless table-primary align-middle">
                                        <thead class="table-light">

                                            <tr>
                                                <th>1</th>
                                                <th>2</th>
                                                <th>3</th>
                                                <th>4</th>
                                                <th>5</th>
                                                <th>6</th>
                                                <th>7</th>
                                                <th>8</th>
                                                <th>9</th>
                                                <th>10</th>
                                                <th>11</th>
                                                <th>12</th>
                                                <th>13</th>
                                                <th>14</th>
                                                <th>15</th>
                                                <th>16</th>
                                                <th>17</th>
                                                <th>18</th>
                                                <th>19</th>
                                                <th>20</th>
                                                <th>21</th>
                                                <th>22</th>
                                                <th>23</th>
                                                <th>24</th>
                                                <th>25</th>
                                                <th>26</th>

                                            </tr>
                                        </thead>

                                        <tbody class="table-group-divider">
                                            <tr class="table-primary">
                                                <th>P-1</th>
                                                <th>P-2</th>
                                                <th>P-3</th>
                                                <th>P-4</th>
                                                <th>P-5</th>
                                                <th>P-6</th>
                                                <th>P-7</th>
                                                <th>P-8</th>
                                                <th>P-9</th>
                                                <th>P-10</th>
                                                <th>P-11</th>
                                                <th>P-12</th>
                                                <th>P-13</th>
                                                <th>P-14</th>
                                                <th>P-15</th>
                                                <th>P-16</th>
                                                <th>P-17</th>
                                                <th>P-18</th>
                                                <th>P-19</th>
                                                <th>P-20</th>
                                                <th>P-21</th>
                                                <th>P-22</th>
                                                <th>P-23</th>
                                                <th>P-24</th>
                                                <th>P-25</th>
                                                <th>P-26</th>


                                            </tr>

                                        </tbody>

                                    </table>

                                </div>

                            </div>
                            <div class="row">
                                <div class="table-responsive">
                                    <table
                                        class="table table-striped table-hover table-borderless table-primary align-middle">
                                        <thead class="table-light">

                                            <tr>
                                                <td id="A"> 27</td>
                                                <td id="A"> 28</td>
                                                <td id="A"> 29</td>
                                                <td id="A"> 30</td>
                                                <td id="A"> 31</td>
                                                <td id="A"> 32</td>
                                                <td id="A"> 33</td>
                                                <td id="A"> 34</td>
                                                <td id="A"> 35</td>
                                                <td id="A"> 36</td>
                                                <td id="A"> 37</td>
                                                <td id="A"> 38</td>
                                                <td id="A"> 39</td>
                                                <td id="A"> 40</td>
                                                <td id="A"> 41</td>
                                                <td id="A"> 42</td>
                                                <td id="A"> 43</td>
                                                <td id="A"> 44</td>
                                                <td id="A"> 45</td>
                                                <td id="A"> 46</td>
                                                <td id="A"> 47</td>
                                                <td id="A"> 48</td>
                                                <td id="A"> 49</td>
                                                <td id="A"> 50</td>
                                                <td id="A"> 51</td>
                                                <td id="A"> 52</td>



                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            <tr class="table-primary">
                                                <td id="A"> P-27</td>
                                                <td id="A"> P-28</td>
                                                <td id="A"> P-29</td>
                                                <td id="A"> P-30</td>
                                                <td id="A"> P-31</td>
                                                <td id="A"> P-32</td>
                                                <td id="A"> P-33</td>
                                                <td id="A"> P-34</td>
                                                <td id="A"> P-35</td>
                                                <td id="A"> P-36</td>
                                                <td id="A"> P-37</td>
                                                <td id="A"> P-38</td>
                                                <td id="A"> P-39</td>
                                                <td id="A"> P-40</td>
                                                <td id="A"> P-41</td>
                                                <td id="A"> P-42</td>
                                                <td id="A"> P-43</td>
                                                <td id="A"> P-44</td>
                                                <td id="A"> P-45</td>
                                                <td id="A"> P-46</td>
                                                <td id="A"> P-47</td>
                                                <td id="A"> P-48</td>
                                                <td id="A"> P-49</td>
                                                <td id="A"> P-50</td>
                                                <td id="A"> P-51</td>
                                                <td id="A"> P-52</td>


                                            </tr>

                                        </tbody>

                                    </table>

                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>

    {{-- Patch Panel B       --}}
    <div class="row">
       <div class="col-12">

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Patch Panel B</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearEquipoModal">
                            Asignar puerto
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <div class="card-body">
                    <div class="row">

                       <div class="col-12">

                            <div class="row">
                                <div class="table-responsive">
                                    <table
                                        class="table table-striped table-hover table-borderless table-primary align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>1</th>
                                                <th>2</th>
                                                <th>3</th>
                                                <th>4</th>
                                                <th>5</th>
                                                <th>6</th>
                                                <th>7</th>
                                                <th>8</th>
                                                <th>9</th>
                                                <th>10</th>
                                                <th>11</th>
                                                <th>12</th>
                                                <th>13</th>
                                                <th>14</th>
                                                <th>15</th>
                                                <th>16</th>
                                                <th>17</th>
                                                <th>18</th>
                                                <th>19</th>
                                                <th>20</th>
                                                <th>21</th>
                                                <th>22</th>
                                                <th>23</th>
                                                <th>24</th>

                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            <tr class="table-primary">
                                                <td id="I"> PP-1</td>
                                                <td id="I"> PP-2</td>
                                                <td id="I"> PP-3</td>
                                                <td id="I"> PP-4</td>
                                                <td id="I"> PP-5</td>
                                                <td id="I"> PP-6</td>
                                                <td id="I"> PP-7</td>
                                                <td id="I"> PP-8</td>
                                                <td id="I"> PP-9</td>
                                                <td id="I"> PP-10</td>
                                                <td id="I"> PP-11</td>
                                                <td id="I"> PP-12</td>
                                                <td id="I"> PP-13</td>
                                                <td id="I"> PP-14</td>
                                                <td id="I"> PP-15</td>
                                                <td id="I"> PP-16</td>
                                                <td id="I"> PP-17</td>
                                                <td id="I"> PP-18</td>
                                                <td id="I"> PP-19</td>
                                                <td id="I"> PP-20</td>
                                                <td id="I"> PP-21</td>
                                                <td id="I"> PP-22</td>
                                                <td id="I"> PP-23</td>
                                                <td id="I"> PP-24</td>

                                            </tr>

                                        </tbody>

                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>

    
    {{-- Patch Panel C       --}}
    <div class="row">
       <div class="col-12">

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Patch Panel C</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearEquipoModal">
                            Asignar puerto
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <div class="card-body">
                    <div class="row">

                       <div class="col-12">

                            <div class="row">
                                <div class="table-responsive">
                                    <table
                                        class="table table-striped table-hover table-borderless table-primary align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>1</th>
                                                <th>2</th>
                                                <th>3</th>
                                                <th>4</th>
                                                <th>5</th>
                                                <th>6</th>
                                                <th>7</th>
                                                <th>8</th>
                                                <th>9</th>
                                                <th>10</th>
                                                <th>11</th>
                                                <th>12</th>
                                                <th>13</th>
                                                <th>14</th>
                                                <th>15</th>
                                                <th>16</th>
                                                <th>17</th>
                                                <th>18</th>
                                                <th>19</th>
                                                <th>20</th>
                                                <th>21</th>
                                                <th>22</th>
                                                <th>23</th>
                                                <th>24</th>

                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            <tr class="table-primary">
                                                <td id="I"> PP-1</td>
                                                <td id="I"> PP-2</td>
                                                <td id="I"> PP-3</td>
                                                <td id="I"> PP-4</td>
                                                <td id="I"> PP-5</td>
                                                <td id="I"> PP-6</td>
                                                <td id="I"> PP-7</td>
                                                <td id="I"> PP-8</td>
                                                <td id="I"> PP-9</td>
                                                <td id="I"> PP-10</td>
                                                <td id="I"> PP-11</td>
                                                <td id="I"> PP-12</td>
                                                <td id="I"> PP-13</td>
                                                <td id="I"> PP-14</td>
                                                <td id="I"> PP-15</td>
                                                <td id="I"> PP-16</td>
                                                <td id="I"> PP-17</td>
                                                <td id="I"> PP-18</td>
                                                <td id="I"> PP-19</td>
                                                <td id="I"> PP-20</td>
                                                <td id="I"> PP-21</td>
                                                <td id="I"> PP-22</td>
                                                <td id="I"> PP-23</td>
                                                <td id="I"> PP-24</td>

                                            </tr>

                                        </tbody>

                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>

        {{-- Patch Panel D       --}}
    <div class="row">
       <div class="col-12">

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Patch Panel D</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearEquipoModal">
                            Asignar puerto
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <div class="card-body">
                    <div class="row">

                       <div class="col-12">

                            <div class="row">
                                <div class="table-responsive">
                                    <table
                                        class="table table-striped table-hover table-borderless table-primary align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>1</th>
                                                <th>2</th>
                                                <th>3</th>
                                                <th>4</th>
                                                <th>5</th>
                                                <th>6</th>
                                                <th>7</th>
                                                <th>8</th>
                                                <th>9</th>
                                                <th>10</th>
                                                <th>11</th>
                                                <th>12</th>
                                                <th>13</th>
                                                <th>14</th>
                                                <th>15</th>
                                                <th>16</th>
                                                <th>17</th>
                                                <th>18</th>
                                                <th>19</th>
                                                <th>20</th>
                                                <th>21</th>
                                                <th>22</th>
                                                <th>23</th>
                                                <th>24</th>

                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            <tr class="table-primary">
                                                <td id="I"> PP-1</td>
                                                <td id="I"> PP-2</td>
                                                <td id="I"> PP-3</td>
                                                <td id="I"> PP-4</td>
                                                <td id="I"> PP-5</td>
                                                <td id="I"> PP-6</td>
                                                <td id="I"> PP-7</td>
                                                <td id="I"> PP-8</td>
                                                <td id="I"> PP-9</td>
                                                <td id="I"> PP-10</td>
                                                <td id="I"> PP-11</td>
                                                <td id="I"> PP-12</td>
                                                <td id="I"> PP-13</td>
                                                <td id="I"> PP-14</td>
                                                <td id="I"> PP-15</td>
                                                <td id="I"> PP-16</td>
                                                <td id="I"> PP-17</td>
                                                <td id="I"> PP-18</td>
                                                <td id="I"> PP-19</td>
                                                <td id="I"> PP-20</td>
                                                <td id="I"> PP-21</td>
                                                <td id="I"> PP-22</td>
                                                <td id="I"> PP-23</td>
                                                <td id="I"> PP-24</td>

                                            </tr>

                                        </tbody>

                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>

        {{-- Patch Panel E      --}}
    <div class="row">
       <div class="col-12">

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Patch Panel E</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearEquipoModal">
                            Asignar puerto
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <div class="card-body">
                    <div class="row">

                       <div class="col-12">

                            <div class="row">
                                <div class="table-responsive">
                                    <table
                                        class="table table-striped table-hover table-borderless table-primary align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>1</th>
                                                <th>2</th>
                                                <th>3</th>
                                                <th>4</th>
                                                <th>5</th>
                                                <th>6</th>
                                                <th>7</th>
                                                <th>8</th>
                                                <th>9</th>
                                                <th>10</th>
                                                <th>11</th>
                                                <th>12</th>
                                                <th>13</th>
                                                <th>14</th>
                                                <th>15</th>
                                                <th>16</th>
                                                <th>17</th>
                                                <th>18</th>
                                                <th>19</th>
                                                <th>20</th>
                                                <th>21</th>
                                                <th>22</th>
                                                <th>23</th>
                                                <th>24</th>

                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            <tr class="table-primary">
                                                <td id="I"> PP-1</td>
                                                <td id="I"> PP-2</td>
                                                <td id="I"> PP-3</td>
                                                <td id="I"> PP-4</td>
                                                <td id="I"> PP-5</td>
                                                <td id="I"> PP-6</td>
                                                <td id="I"> PP-7</td>
                                                <td id="I"> PP-8</td>
                                                <td id="I"> PP-9</td>
                                                <td id="I"> PP-10</td>
                                                <td id="I"> PP-11</td>
                                                <td id="I"> PP-12</td>
                                                <td id="I"> PP-13</td>
                                                <td id="I"> PP-14</td>
                                                <td id="I"> PP-15</td>
                                                <td id="I"> PP-16</td>
                                                <td id="I"> PP-17</td>
                                                <td id="I"> PP-18</td>
                                                <td id="I"> PP-19</td>
                                                <td id="I"> PP-20</td>
                                                <td id="I"> PP-21</td>
                                                <td id="I"> PP-22</td>
                                                <td id="I"> PP-23</td>
                                                <td id="I"> PP-24</td>

                                            </tr>

                                        </tbody>

                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
    {{-- Patch Panel F      --}}
    <div class="row">
       <div class="col-12">

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Patch Panel F</h3>

                    <div class="card-tools">
                        <!-- Botón para abrir el modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#crearEquipoModal">
                            Asignar puerto
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <div class="card-body">
                    <div class="row">

                       <div class="col-12">

                            <div class="row">
                                <div class="table-responsive">
                                    <table
                                        class="table table-striped table-hover table-borderless table-primary align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>1</th>
                                                <th>2</th>
                                                <th>3</th>
                                                <th>4</th>
                                                <th>5</th>
                                                <th>6</th>
                                                <th>7</th>
                                                <th>8</th>
                                                <th>9</th>
                                                <th>10</th>
                                                <th>11</th>
                                                <th>12</th>
                                                <th>13</th>
                                                <th>14</th>
                                                <th>15</th>
                                                <th>16</th>
                                                <th>17</th>
                                                <th>18</th>
                                                <th>19</th>
                                                <th>20</th>
                                                <th>21</th>
                                                <th>22</th>
                                                <th>23</th>
                                                <th>24</th>

                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            <tr class="table-primary">
                                                <td id="I"> PP-1</td>
                                                <td id="I"> PP-2</td>
                                                <td id="I"> PP-3</td>
                                                <td id="I"> PP-4</td>
                                                <td id="I"> PP-5</td>
                                                <td id="I"> PP-6</td>
                                                <td id="I"> PP-7</td>
                                                <td id="I"> PP-8</td>
                                                <td id="I"> PP-9</td>
                                                <td id="I"> PP-10</td>
                                                <td id="I"> PP-11</td>
                                                <td id="I"> PP-12</td>
                                                <td id="I"> PP-13</td>
                                                <td id="I"> PP-14</td>
                                                <td id="I"> PP-15</td>
                                                <td id="I"> PP-16</td>
                                                <td id="I"> PP-17</td>
                                                <td id="I"> PP-18</td>
                                                <td id="I"> PP-19</td>
                                                <td id="I"> PP-20</td>
                                                <td id="I"> PP-21</td>
                                                <td id="I"> PP-22</td>
                                                <td id="I"> PP-23</td>
                                                <td id="I"> PP-24</td>

                                            </tr>

                                        </tbody>

                                    </table>
                                </div>

                            </div>
                            <div class="row">
                                <div class="table-responsive">
                                    <table
                                        class="table table-striped table-hover table-borderless table-primary align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>25</th>
                                                <th>26</th>
                                                <th>27</th>
                                                <th>28</th>
                                                <th>29</th>
                                                <th>30</th>
                                                <th>31</th>
                                                <th>32</th>
                                                <th>33</th>
                                                <th>34</th>
                                                <th>35</th>
                                                <th>36</th>
                                                <th>37</th>
                                                <th>38</th>
                                                <th>39</th>
                                                <th>40</th>
                                                <th>41</th>
                                                <th>42</th>
                                                <th>43</th>
                                                <th>44</th>
                                                <th>45</th>
                                                <th>46</th>
                                                <th>47</th>
                                                <th>48</th>

                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            <tr class="table-primary">
                                                <td id="I"> 25 </td>
                                                <td id="I"> 26 </td>
                                                <td id="I"> 27 </td>
                                                <td id="I"> 28 </td>
                                                <td id="I"> 29 </td>
                                                <td id="I"> 30 </td>
                                                <td id="I"> 31 </td>
                                                <td id="I"> 32 </td>
                                                <td id="I"> 33 </td>
                                                <td id="I"> 34 </td>
                                                <td id="I"> 35 </td>
                                                <td id="I"> 36 </td>
                                                <td id="I"> 37 </td>
                                                <td id="I"> 38 </td>
                                                <td id="I"> 39 </td>
                                                <td id="I"> 40 </td>
                                                <td id="I"> 41 </td>
                                                <td id="I"> 42 </td>
                                                <td id="I"> 43 </td>
                                                <td id="I"> 44 </td>
                                                <td id="I"> 45 </td>
                                                <td id="I"> 46 </td>
                                                <td id="I"> 47 </td>
                                                <td id="I"> 48 </td>

                                            </tr>

                                        </tbody>

                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Fondo transparente y sin borde en el contenedor */
        #example1_wrapper .dt-buttons {
            background-color: transparent;
            box-shadow: none;
            border: none;
            display: flex;
            justify-content: center;
            /* Centrar los botones */
            gap: 10px;
            /* Espaciado entre botones */
            margin-bottom: 15px;
            /* Separar botones de la tabla */
        }

        /* Estilo personalizado para los botones */
        #example1_wrapper .btn {
            color: #fff;
            /* Color del texto en blanco */
            border-radius: 4px;
            /* Bordes redondeados */
            padding: 5px 15px;
            /* Espaciado interno */
            font-size: 14px;
            /* Tamaño de fuente */
        }

        #A {
            background-color: #28a745;
            border: #dc3545;
        }

        #I {
            background-color: #6f7470;
        }

        /* Colores por tipo de botón */
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-info {
            background-color: #17a2b8;
            border: none;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
            border: none;
        }

        .btn-default {
            background-color: #6e7176;
            color: #212529;
            border: none;
        }
    </style>
@stop

@section('js')
    <script>
        // Inicialización de Select2
        $(document).ready(function() {
            $('#select_emp').select2({
                placeholder: "Selecciona un empleado",
                allowClear: true,
                // para que se ajuste al contenedor padre
            });
        });
    </script>
    <script>
        $(function() {
            $("#example1").DataTable({
                "pageLength": 5,
                "language": {
                    "emptyTable": "No hay información",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ equipos",
                    "infoEmpty": "Mostrando 0 a 0 de 0 equipos",
                    "infoFiltered": "(Filtrado de _MAX_ total equipos)",
                    "lengthMenu": "Mostrar _MENU_ equipos",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscador:",
                    "zeroRecords": "Sin resultados encontrados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                buttons: [{
                        text: '<i class="fas fa-copy"></i> COPIAR',
                        extend: 'copy',
                        className: 'btn btn-default'
                    },
                    {
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        extend: 'pdf',
                        className: 'btn btn-danger'
                    },
                    {
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        extend: 'csv',
                        className: 'btn btn-info'
                    },
                    {
                        text: '<i class="fas fa-file-excel"></i> EXCEL',
                        extend: 'excel',
                        className: 'btn btn-success'
                    },
                    {
                        text: '<i class="fas fa-print"></i> IMPRIMIR',
                        extend: 'print',
                        className: 'btn btn-warning'
                    }
                ]
            }).buttons().container().appendTo('#example1_wrapper .row:eq(0)');
        });
    </script>
    <script>
        $('#marca_id').on('change', function() {
            let marcaID = $(this).val();
            // alert(marcaID)
            if (marcaID) {
                $.get("{{ url('/admin/equipos/getModelos') }}/" + marcaID, function(data) {
                    $('#modelo').empty().append('<option value="">Seleccione un modelo</option>');
                    data.forEach(getmodelo => {
                        $('#get').append('<option value="' + getmodelo.id + '">' + getmodelo
                            .nombre + '</option>');
                    });
                });
            } else {
                $('#modelo').empty().append('<option value="">Seleccione un modelo</option>');
            }
            console.log(modelo)
        });
    </script>
    <script>
        function checkPing() {
            fetch("{{ url('/admin/equipos/ping') }}/" + '192.168.10.11') // cambia la IP aquí
                .then(response => response.json())
                .then(data => {
                    const div = document.getElementById('statusDiv');
                    if (data.status === 'online') {
                        div.style.backgroundColor = 'green';
                    } else {
                        div.style.backgroundColor = 'red';
                    }

                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Llamarlo cada 5 segundos
        setInterval(checkPing, 5000);
    </script>
@stop
