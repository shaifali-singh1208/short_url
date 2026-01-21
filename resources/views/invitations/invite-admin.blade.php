  @inject('Userobj', 'App\Models\User')

  <x-app-layout>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="col-md-4">
              <div class="card border-0 shadow-sm sticky-top" style="top: 20px; border-radius: 12px;">
                  <div class="card-header bg-white border-bottom py-3" style="border-radius: 12px 12px 0 0;">
                      <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                          <span class="bg-primary rounded-pill me-2"
                              style="width: 4px; height: 18px; display: inline-block;"></span>
                          {{ Auth::user()->isSuperAdmin() ? 'Invite New Client' : 'Invite Team Member' }}
                      </h6>
                  </div>

                  <div class="card-body p-4">
                      <form action="{{ route('invitations.store') }}" method="POST">
                          @csrf

                          <div class="form-group mb-3">
                              <label class="form-label small fw-bold text-uppercase text-muted"
                                  style="font-size: 11px;">Full Name</label>
                              <input type="text" name="name"
                                  class="form-control form-control-lg fs-6 bg-light border-0" placeholder="Enter name"
                                  style="border-radius: 8px;" required>
                          </div>

                          <div class="form-group mb-3">
                              <label class="form-label small fw-bold text-uppercase text-muted"
                                  style="font-size: 11px;">Email Address</label>
                              <input type="email" name="email"
                                  class="form-control form-control-lg fs-6 bg-light border-0"
                                  placeholder="Enter Email here" style="border-radius: 8px;" required>
                          </div>


                          <div class="form-group mb-3">
                              <label class="form-label small fw-bold text-uppercase text-muted"
                                  style="font-size: 11px;">New Company Name</label>
                              <input type="text" name="company_name" class="form-control bg-light border-0"
                                  placeholder="Enter Company" style="border-radius: 8px;">
                          </div>


                          <div class="form-group pt-2">
                              <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm"
                                  style="border-radius: 8px; background-color: #0d6efd; border: none;">
                                  Send Invitation
                              </button>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
      </div>

  </x-app-layout>
