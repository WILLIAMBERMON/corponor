<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
 <p class="mb-0 text-muted">Se conserva la última revisión. Solo se comprueban enlaces al pulsar «Volver a revisar».</p>
 <button type="button" class="btn btn-outline-primary" id="iniciar-revision">Volver a revisar</button>
</div>
<div id="revision-estado" class="alert alert-info" role="status" aria-live="polite">
<?php if($ultima): ?>Última revisión: <?= html_escape($ultima->iniciada_en) ?> · Estado: <?= html_escape($ultima->estado) ?> · Revisados: <?= (int)$ultima->revisados ?> · Novedades detectadas: <?= (int)$ultima->novedades ?>. Las corregidas se ocultan.
<?php else: ?>No hay revisiones anteriores. Pulse «Volver a revisar» para iniciar la primera.<?php endif; ?>
</div>
<button id="revision-reintentar" class="btn btn-outline-primary mb-3" hidden>Continuar revisión interrumpida</button>
<div class="card"><div class="card-body table-responsive">
<table id="revisionTabla" class="table table-hover align-middle w-100">
<thead><tr><th>Tipo de documento</th><th>Documento</th><th>Grupo</th><th>Origen</th><th>Estado</th><th>Detalle</th><th>Acción</th></tr></thead>
<tbody>
<?php foreach($novedades as $n): ?>
<tr><td><?= $n->tipo_documento==='acuerdo'?'Acuerdo':'Resolución' ?></td><td><?= html_escape($n->titulo) ?></td><td><?= html_escape($n->grupo) ?></td><td><?= html_escape($n->tipo) ?></td><td><?= html_escape($n->estado) ?></td><td><?= html_escape($n->detalle) ?></td><td><a class="btn btn-sm btn-outline-primary" href="<?= site_url($n->tipo_documento==='acuerdo'?'admin/acuerdos/editar/'.(int)$n->acuerdo_id:'admin/resoluciones/editar/'.(int)$n->resolucion_id) ?>" target="_blank" rel="noopener">Corregir</a></td></tr>
<?php endforeach; ?>
</tbody></table></div></div>
<script>
document.addEventListener('DOMContentLoaded',function(){
 const iniciar=document.getElementById('iniciar-revision'),reintentar=document.getElementById('revision-reintentar'),estado=document.getElementById('revision-estado');
 const inicio=<?= json_encode(site_url('admin/enlaces/iniciar')) ?>,lote=<?= json_encode(site_url('admin/enlaces/lote')) ?>;
 const editarResolucion=<?= json_encode(site_url('admin/resoluciones/editar/')) ?>,editarAcuerdo=<?= json_encode(site_url('admin/acuerdos/editar/')) ?>;
 let csrfName=<?= json_encode($this->security->get_csrf_token_name()) ?>,csrfHash=<?= json_encode($this->security->get_csrf_hash()) ?>;
 let revisionId=0,trabajando=false;
 const tabla=window.jQuery && jQuery.fn.DataTable ? jQuery('#revisionTabla').DataTable({pageLength:10,lengthMenu:[10,25,50,100],order:[],language:{search:'Buscar:',lengthMenu:'Mostrar _MENU_',info:'Mostrando _START_ a _END_ de _TOTAL_',infoEmpty:'Sin registros',zeroRecords:'No se encontraron registros',paginate:{first:'Primero',last:'Último',next:'Siguiente',previous:'Anterior'}}}) : null;
 const cuerpo=document.querySelector('#revisionTabla tbody');
 function vaciar(){if(tabla)tabla.clear().draw();else cuerpo.replaceChildren();}
 function agregar(item){
  const r=item.documento,v=item.resultado,tipo=item.tipo_documento;
  const a=document.createElement('a');a.className='btn btn-sm btn-outline-primary';a.textContent='Corregir';a.target='_blank';a.rel='noopener';
  a.href=(tipo==='acuerdo'?editarAcuerdo:editarResolucion).replace(/\/$/,'')+'/'+Number(r.id);
  const valores=[tipo==='acuerdo'?'Acuerdo':'Resolución',r.titulo+' ('+r.fecha_resolucion+')',r.grupo_descripcion,v.tipo,v.estado,v.detalle];
  if(tabla){const cells=valores.map(x=>{const span=document.createElement('span');span.textContent=x||'';return span.outerHTML});cells.push(a.outerHTML);tabla.row.add(cells).draw(false);}
  else {const tr=document.createElement('tr');valores.forEach(x=>{const td=document.createElement('td');td.textContent=x||'';tr.appendChild(td)});const td=document.createElement('td');td.appendChild(a);tr.appendChild(td);cuerpo.appendChild(tr);}
 }
 async function post(url,extra){const body=new URLSearchParams(extra);body.set(csrfName,csrfHash);
  const res=await fetch(url,{method:'POST',credentials:'same-origin',cache:'no-store',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:body.toString()});
  if(!res.ok||res.redirected)throw new Error('HTTP '+res.status);
  const data=await res.json();if(data.csrf_hash)csrfHash=data.csrf_hash;return data;
 }
 async function revisar(nueva){if(trabajando)return;trabajando=true;iniciar.disabled=true;reintentar.hidden=true;
  try{if(nueva||!revisionId){const d=await post(inicio,{});revisionId=d.revision_id;vaciar();}
   while(true){const d=await post(lote,{revision_id:String(revisionId)});
    estado.className='alert alert-info';estado.textContent='Revisados: '+d.revisados+' · Novedades: '+d.novedades;
    if(d.novedad)agregar(d.novedad);
    if(d.fin){estado.className=d.novedades?'alert alert-warning':'alert alert-success';estado.textContent+=' · Revisión finalizada. Actualice para ocultar las corregidas.';break;}
    await new Promise(resolve=>setTimeout(resolve,25));
   }
  }catch(e){estado.className='alert alert-warning';estado.textContent='Revisión interrumpida: '+e.message+'. Puede continuar.';reintentar.hidden=false;}
  finally{trabajando=false;iniciar.disabled=false;}
 }
 iniciar.addEventListener('click',()=>revisar(true));reintentar.addEventListener('click',()=>revisar(false));
 // Al volver de editar en otra pestaña, refrescar solo el listado persistido, sin revisar URLs.
 window.addEventListener('focus',()=>{if(!trabajando && document.visibilityState==='visible' && window.__corponorEnlacesEditado){window.__corponorEnlacesEditado=false;location.reload();}});
 document.querySelector('#revisionTabla').addEventListener('click',e=>{if(e.target.closest('a[target="_blank"]'))window.__corponorEnlacesEditado=true;});
});
</script>
