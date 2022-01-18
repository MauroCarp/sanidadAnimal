// BUSCAR MATRICULA EN INFORMES
const formsBuscarMatricula = document.getElementsByClassName('formBuscarMatricula')

if(formsBuscarMatricula.length > 0){

    for (const btn of formsBuscarMatricula) {
        
        btn.addEventListener('submit',(e)=>{

            e.preventDefault()
            
            let informeId = e.target[0].id
            
            let informeNum = informeId.replace('matriculaInforme','')
            
            let matricula = document.getElementById(informeId).value
                        
            if(matricula != ''){

                let matriculaValida = (matricula.match(/[0-9][-]...([\d{4}])/) && matricula.length == 6)

                if(matriculaValida){
            
                    if(informeNum == 3)
                        window.open(`extensiones/fpdf/informesPdf.php?informe=informe${informeNum}&matricula=${matricula}`)
                    else
                        window.location = `index.php?ruta=aftosa/informes/informe${informeNum}&matricula=${matricula}`
                        
                    
                }else{

                    swal({
                        type: "error",
                        title: "El formato de la matricula no es el correcto",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                        })

                }
        
            }else{

                swal({
                    type: "error",
                    title: "El campo Matricula esta Vacio",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                    })

            }        
            
        })
        
        
    }
}

// ENVIAR CRONOGRAMA POR MAIL

const btnEnviarMail = document.getElementsByClassName('enviarMail')

for (const btn of btnEnviarMail) {
    
    btn.addEventListener('click',(e)=>{

        let informeNum
        
        let matricula

        console.log(e);
        
        if(e.path[0].attributes.length > 0){
        
            informeNum =  e.path[0].attributes.informe.value 
            matricula =  e.path[0].attributes.matricula.value 
            
        }else{
            
            informeNum = e.path[1].attributes.informe.value
            matricula =  e.path[1].attributes.matricula.value
        
        }

        swal({
            title: "Enviar Cronograma por E-mail?",
            text: "Si no estas seguro, podes cancelar esta accion.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Si, enviar Cronograma!"
        })
        .then(function(result){
                
            if (result.value) {
            
                let url = 'extensiones/fpdf/informesPdf.php'
                
                let data = new FormData();
                data.append('matricula',matricula)
                data.append('informe',`informe${informeNum}`)
                data.append('mail',true)
                
                fetch(url,{
                    method:'post',
                    body:data
                }).then(res=>res.json())
                    .then(respuesta => {
                        
                        if(respuesta == 'ok'){

                            let url = 'ajax/informes.ajax.php'

                            let data = new FormData()
                            data.append('mail',true)
                            data.append('matricula',matricula)

                            fetch(url,{
                                method:'post',
                                body:data
                            }).then(resp => resp.json())
                            .then(respuesta=>console.log(respuesta))
                            .catch(err=>console.log(err))

                        }

                    })

                .catch(err => console.log(err))

            }
    
        })

    })

}

