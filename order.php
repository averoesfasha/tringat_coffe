<?php
require 'config.php';
$pageTitle='Pesan Menu';
$menus=$pdo->query("SELECT * FROM menu WHERE active=1 ORDER BY FIELD(category,'Coffee','Non Coffee','Mocktail','Tea Series','Add On','Snack'),name")->fetchAll();
$error='';
$drinkCategories=['Coffee','Non Coffee','Mocktail','Tea Series'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 $name=trim($_POST['customer_name']??'');
 $type=($_POST['order_type']??'Take Away')==='Dine In'?'Dine In':'Take Away';
 $qty=$_POST['qty']??[]; $temps=$_POST['temperature']??[]; $sugars=$_POST['sugar_level']??[]; $chosen=[]; $total=0;
 if($name==='') $error='Nama pelanggan wajib diisi.';
 foreach($menus as $m){
  $q=max(0,min(20,(int)($qty[$m['id']]??0)));
  if($q>0){
   $isDrink=in_array($m['category'],$drinkCategories,true);
   $temp=$isDrink?(($temps[$m['id']]??'Iced')==='Hot'?'Hot':'Iced'):null;
   $sugar=$isDrink?($sugars[$m['id']]??'Normal Sugar'):'N/A';
   if($isDrink && !in_array($sugar,['Normal Sugar','Less Sugar','Non Sugar'],true)) $sugar='Normal Sugar';
   $chosen[]=['menu'=>$m,'qty'=>$q,'subtotal'=>$m['price']*$q,'temperature'=>$temp,'sugar_level'=>$sugar];
   $total+=$m['price']*$q;
  }
 }
 if(!$chosen && !$error)$error='Pilih minimal satu menu terlebih dahulu.';
 if(!$error){
  try{$pdo->beginTransaction();
   // Nomor antrean harian; id pesanan menjadi dasar urutan FIFO di dashboard.
   $next=(int)$pdo->query('SELECT COALESCE(MAX(queue_no),0)+1 FROM orders WHERE DATE(created_at)=CURDATE()')->fetchColumn();
   $st=$pdo->prepare('INSERT INTO orders(queue_no,customer_name,order_type,total) VALUES(?,?,?,?)');$st->execute([$next,$name,$type,$total]);$oid=(int)$pdo->lastInsertId();
   $it=$pdo->prepare('INSERT INTO order_items(order_id,menu_id,menu_name,price,quantity,subtotal,temperature,sugar_level) VALUES(?,?,?,?,?,?,?,?)');
   foreach($chosen as $c)$it->execute([$oid,$c['menu']['id'],$c['menu']['name'],$c['menu']['price'],$c['qty'],$c['subtotal'],$c['temperature'],$c['sugar_level']]);
   $pdo->commit();header('Location: order_success.php?id='.$oid);exit;
  }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();$error='Pesanan belum dapat disimpan. Pastikan database sudah diimpor dari database.sql terbaru.';}
 }
}
require 'includes/header.php'; ?>
<span class="eyebrow">Tringat Coffee</span><h1 class="page-title">Pesan menu favorit</h1><p class="muted">Pilih menu dan jumlah. Minuman dapat disesuaikan suhu dan tingkat gulanya. Pesanan diproses berdasarkan FIFO.</p>
<?php if($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" class="layout"><section class="card"><div class="form-row"><label for="customer_name">Nama pelanggan</label><input class="input" id="customer_name" name="customer_name" required maxlength="120" placeholder="Masukkan nama" value="<?= htmlspecialchars($_POST['customer_name']??'') ?>"></div><div class="form-row"><label for="order_type">Jenis pesanan</label><select id="order_type" name="order_type"><option <?= (($_POST['order_type']??'Take Away')==='Take Away'?'selected':'') ?>>Take Away</option><option <?= (($_POST['order_type']??'')==='Dine In'?'selected':'') ?>>Dine In</option></select></div><hr class="divider"><h2 style="font-family:'Playfair Display'">Pilih menu</h2>
<?php $currentCategory=''; foreach($menus as $m): if($currentCategory!==$m['category']): $currentCategory=$m['category']; ?><h3 style="margin-top:24px"><?= htmlspecialchars($currentCategory) ?></h3><?php endif; $isDrink=in_array($m['category'],$drinkCategories,true); ?>
<div class="item-row"><div style="flex:1"><strong><?= htmlspecialchars($m['name']) ?></strong><div class="muted small"><?= htmlspecialchars($m['description']) ?></div><div class="price"><?= rupiah($m['price']) ?></div>
<?php if($isDrink): ?><div class="form-row" style="margin:9px 0 0"><label for="temp_<?= $m['id'] ?>">Suhu</label><select id="temp_<?= $m['id'] ?>" name="temperature[<?= $m['id'] ?>]"><option <?= (($_POST['temperature'][$m['id']]??'Iced')==='Iced'?'selected':'') ?>>Iced</option><option <?= (($_POST['temperature'][$m['id']]??'')==='Hot'?'selected':'') ?>>Hot</option></select></div><div class="form-row" style="margin:9px 0 0"><label for="sugar_<?= $m['id'] ?>">Tingkat gula</label><select id="sugar_<?= $m['id'] ?>" name="sugar_level[<?= $m['id'] ?>]"><?php foreach(['Normal Sugar','Less Sugar','Non Sugar'] as $level): ?><option <?= (($_POST['sugar_level'][$m['id']]??'Normal Sugar')===$level?'selected':'') ?>><?= $level ?></option><?php endforeach; ?></select></div><?php endif; ?></div><label class="small">Qty<br><input class="quantity" type="number" name="qty[<?= $m['id'] ?>]" min="0" max="20" value="<?= (int)($_POST['qty'][$m['id']]??0) ?>"></label></div>
<?php endforeach; ?></section><aside class="card"><span class="eyebrow">Sebelum dikirim</span><h2 style="font-family:'Playfair Display'">Ringkasan</h2><p class="muted">Harga contoh ditampilkan untuk awal dan dapat diubah admin. Total dihitung saat pesanan dikirim.</p><div class="notice">FIFO: pesanan yang masuk lebih dulu akan diproses lebih dulu.</div><button class="btn" style="width:100%" type="submit">Kirim Pesanan →</button><p class="muted small">Nomor antrean dibuat otomatis setelah pesanan berhasil disimpan.</p></aside></form>
<?php require 'includes/footer.php'; ?>
