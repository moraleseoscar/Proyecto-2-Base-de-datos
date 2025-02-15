import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { GeneralService } from 'src/app/services/general.service';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-edit-create-profile',
  templateUrl: './edit-create-profile.component.html',
  styleUrls: ['./edit-create-profile.component.scss'],
})
export class EditCreateProfileComponent implements OnInit {
  public profile_form: FormGroup;
  public icons: Array<any> = [
    { icon: 'profile1.png', class: '' },
    { icon: 'profile2.png', class: '' },
    { icon: 'profile3.png', class: '' },
    { icon: 'profile4.png', class: '' },
    { icon: 'profile5.png', class: '' },
    { icon: 'profile6.png', class: '' },
    { icon: 'profile7.png', class: '' },
    { icon: 'profile8.png', class: '' },
    { icon: 'profile9.png', class: '' },
  ];
  public icon_selected: any = null;
  public id: string = '';
  public title: string = 'Crear';

  constructor(
    private route: ActivatedRoute,
    private general_service: GeneralService,
    private router: Router,
    private spinner: NgxSpinnerService
  ) {
    this.profile_form = this.createFormGroup();
    this.id = this.route.snapshot.params['id'];
  }

  ngOnInit(): void {
    if (this.id) {
      this.editFormGroup();
      this.title = 'Editar'
    }
  }

  createFormGroup() {
    return new FormGroup({
      nombre: new FormControl('', [Validators.required]),
    });
  }

  editFormGroup() {
    this.general_service.getAuth('profile/' + this.id).then((res) => {
      this.profile_form.patchValue({ nombre: res.data.nombre });
      const index = this.icons.findIndex((e) => (e.icon == res.data.icon));
      this.selectIcon(index);
    });
  }

  selectIcon(index: number): void {
    this.icon_selected = this.icons[index].icon;
    this.icons.forEach((e) => (e.class = ''));
    this.icons[index].class = 'icon--selected';
  }

  submit() {
    this.spinner.show();
    const data = Object.assign(this.profile_form.value, {
      icon: this.icon_selected,
    });
    console.log(data);
    if (this.id) {
      this.general_service
        .putAuth('profile/' + this.id, data)
        .then((res) => {
          this.spinner.hide();
          Swal.fire({
            icon: 'success',
            title: res.message,
            confirmButtonText: 'Aceptar',
          }).then((result) => {
            if (result.isConfirmed) {
              this.router.navigate(['/profiles']);
            }
          });
        })
        .catch((err) => {
          this.spinner.hide();
          console.log(err);
          Swal.fire({
            icon: 'error',
            title: '¡Atención!',
            showCancelButton: false,
            text: 'Por favor, inténtelo más tarde.',
            confirmButtonText: 'Aceptar',
          });
        });
    } else {
      this.general_service
        .postAuth('profile', data)
        .then((res) => {
          this.spinner.hide();
          Swal.fire({
            icon: 'success',
            title: res.message,
            confirmButtonText: 'Aceptar',
          }).then((result) => {
            if (result.isConfirmed) {
              this.router.navigate(['/profiles']);
            }
          });
        })
        .catch((err) => {
          this.spinner.hide();
          console.log(err);
          Swal.fire({
            icon: 'error',
            title: '¡Atención!',
            showCancelButton: false,
            text: 'Por favor, inténtelo más tarde.',
            confirmButtonText: 'Aceptar',
          });
        });
    }
  }

  get nombre() {
    return this.profile_form.get('nombre');
  }
}
