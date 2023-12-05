@extends('layouts.mainLogin')

@section('title', 'Iniciar Sesión')

@section('content')

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                    <div class="row"></div>
                    <img class="logo-dmi">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">¡Iniciar sesión!</h1>
                  </div>
                  <form class="user" method='POST' action="{{ route('postlogin')}}">
                  @csrf
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user  @error('usuario') is-invalid @enderror" value="{{ old('usuario') }}" id="InputUsuario"
                      name="usuario" 
                      aria-describedby="emailHelp" 
                      placeholder="Ingresa tu usuario">
                      @error('usuario')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                      @enderror
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user @error('password') is-invalid @enderror"
                       id="exampleInputPassword"
                       name="password"
                        placeholder="Ingresa tu Contraseña">
                        @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                    </div>
                    <div class="form-group">
                      <div class="custom-control custom-checkbox small">
                      <input class="form-check-input custom-control-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                          <label class="form-check-label custom-control-label" for="remember">
                              {{ __('Recuerdame') }}
                          </label>
                      
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-user btn-block">
                  Iniciar Sesion
                    </button>
                    <hr>
                    <!-- <a href="index.html" class="btn btn-google btn-user btn-block">
                      <i class="fab fa-google fa-fw"></i> Login with Google
                    </a>
                    <a href="index.html" class="btn btn-facebook btn-user btn-block">
                      <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                    </a> -->
                  </form>
                  <hr>
                  <!-- <div class="text-center">
                    <a class="small" href="">Forgot Password?</a>
                  </div> -->
                  <!-- <div class="text-center">
                    <a class="small" href="register.html">Create an Account!</a>
                  </div> -->
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>



  <!-- Bootstrap core JavaScript-->
  <script src="{{ asset('/customSB/vendor/jquery/jquery.min.js')}}"></script>
  <script src="{{ asset('/customSB/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{ asset('/customSB/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

  <!-- Custom scripts for all pages-->
  <!-- <script src="js/sb-admin-2.min.js"></script> -->


  @endsection