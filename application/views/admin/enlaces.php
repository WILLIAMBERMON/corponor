<div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
 <p class="mb-0 text-muted">Al ingresar se muestra la última revisión guardada. La comprobación solo se ejecuta al pulsar el botón.</p>
 <button type="button" class="btn btn-outline-primary" id="iniciar-revision">Volver a revisar</button>
</div>
<div id="revision-estado" class="alert alert-info" role="status" aria-live="polite">
<?php if($ultima): ?>Última revisión: <?= html_escape($ultima->iniciada_en) ?> · Estado: <?= html_escape($ultima->estado) ?> · Revisadas: <?= (int)$ultima->revisados ?> · Novedades detectadas: <?= (int)$ultima->novedades ?>. Las corregidas desde el módulo se ocultan del listado.
<?php else: ?>No hay revisiones anteriores. Pulse «Volver a revisar» para iniciar la primera.<?php endif; ?>
</div>
<button id="revision-reintentar" class="btn btn-outline-primary mb-3" hidden>Continuar revisión interrumpida</button>
<div class="card"><div class="card-body table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Resolución</th><th>Grupo</th><th>Origen</th><th>Estado</th><th>Detalle</th><th></th></tr></thead><tbody id="revision-resultados">
<?php foreach($novedades as $n): ?><tr><td><?= html_escape($n->titulo) ?></td><td><?= html_escape($n->grupo) ?></td><td><?= html_escape($n->tipo) ?></td><td><?= html_escape($n->estado) ?></td><td><?= html_escape($n->detalle) ?></td><td><a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/resoluciones/editar/'.(int)$n->resolucion_id) ?>" target="_blank" rel="noopener">Corregir</a></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<script>
(function(){
 const iniciar=document.getElementById('iniciar-revision'), reintentar=document.getElementById('revision-reintentar');
 const estado=document.getElementById('revision-estado'), filas=document.getElementById('revision-resultados');
 const endpointInicio=<?= json_encode(site_url('admin/enlaces/iniciar')) ?>, endpointLote=<?= json_encode(site_url('admin/enlaces/lote')) ?>;
 const editar=<?= json_encode(site_url('admin/resoluciones/editar/')) ?>;
 let csrfName=<?= json_encode($this->security->get_csrf_token_name()) ?>, csrfHash=<?= json_encode($this->security->get_csrf_hash()) ?>;
 let revisionId=0, trabajando=false;
 async function post(url,extra){ const body=new URLSearchParams(extra); body.set(csrfName,csrfHash);
  const response=await fetch(url,{method:'POST',credentials:'same-origin',cache:'no-store',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:body.toString()});
  if(!response.ok||response.redirected) throw new Error('Error HTTP '+response.status);
  const data=await response.json(); if(data.csrf_hash) csrfHash=data.csrf_hash; return data; }
 function agregar(item){const r=item.resolucion,v=item.resultado,tr=document.createElement('tr');
  [r.titulo+' ('+r.fecha_resolucion+')',r.grupo_descripcion,v.tipo,v.estado,v.detalle].forEach(value=>{const td=document.createElement('td');td.textContent=value;tr.appendChild(td)});
  const td=document.createElement('td'),a=document.createElement('a');a.href=editar.replace(/\/$/,'')+'/'+Number(r.id);a.textContent='Corregir';a.className='btn btn-sm btn-outline-primary';a.target='_blank';a.rel='noopener';td.appendChild(a);tr.appendChild(td);filas.appendChild(tr);
 }
 async function revisar(nueva){if(trabajando)return; trabajando=true;iniciar.disabled=true;reintentar.hidden=true;
  try { if(nueva||!revisionId){const data=await post(endpointInicio,{});revisionId=data.revision_id;filas.replaceChildren();}
   while(true){const d=await post(endpointLote,{revision_id:String(revisionId)});
    estado.className='alert alert-info';estado.textContent='Revisadas: '+d.revisados+'. Novedades: '+d.novedades+'.';
    if(d.novedad) agregar(d.novedad);
    if(d.fin){estado.className=d.novedades?'alert alert-warning':'alert alert-success';estado.textContent+=' Revisión finalizada. Actualice la página para ver únicamente las novedades aún pendientes.';break;}
   }
  }catch(e){estado.className='alert alert-warning';estado.textContent='Revisión interrumpida: '+e.message+'. Puede continuar sin perder los resultados guardados.';reintentar.hidden=false;}
  finally{trabajando=false;iniciar.disabled=false;}
 }
 iniciar.addEventListener('click',()=>revisar(true));reintentar.addEventListener('click',()=>revisar(false));
})();
</script>
