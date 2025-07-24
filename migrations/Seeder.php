<?php

namespace App\Migration;

require_once 'vendor/autoload.php';
require_once 'app/config/env.php';

use App\Core\implements\CloudinaryUploader;
use App\Core\Uploader;
use Cloudinary\Cloudinary;
use PDO;

use function App\Config\dump_die;


// class Seeder
// {
//   private PDO $pdo;
//   private Cloudinary $cloudinary;


//   public function __construct(\PDO $pdo)
//   {
//     $this->pdo = $pdo;
//     $this->cloudinary = new Cloudinary([
//       'cloud' => [
//         'cloud_name' => CLOUD_NAME,
//         'api_key'    => PUBLIC_KEY,
//         'api_secret' => PRIVATE_KEY
//       ]
//     ]);
//   }


//   private function hashPassword(): string
//   {
//     return password_hash('Passer123', PASSWORD_BCRYPT);
//   }

//   public function seed(): void
//   {

//     $uploadPath = __DIR__ . '/../public/images/upload/';


//     // === UTILISATEURS ===
//     $utilisateurs = [
//       // nom, prenom, cni, lieu_naissance, date_naissane, fichier_local
//       // ['DIA', 'Moussa', '2020999999999', 'Dakar', '1990-01-01', 'file_687c28e10a18d6.47321031.png'],
//       // ['Sow', 'Aminata', '2020000000001', 'Saint-Louis', '1992-05-12', 'aminata_cni.png'],
//       // ['Diop', 'Cheikh', '2020000000002', 'Kaolack', '1988-11-23', 'cheikh_cni.png'],
//       // ['Diallo', 'Moussa', '2020123456789', 'Thiès', '1995-07-15', 'moussa_cni.png'],
//       // ['Traoré', 'Fatoumata', '2020987654321', 'Ziguinchor', '1993-03-30', 'fatou_cni.png'],
//       // ['Diallo', 'Abdoul', '4111161818112', 'Matam', '1991-09-09', 'abdoul_cni.png'],
//       ['AW', 'Aboubacrine', '129019990275', 'Podor', '1994-12-21', 'aboubacrine_cni.png'],
//       // ['Diallo', 'Jeeri', '7777', 'Kolda', '1996-06-06', 'cni_2.jpg'],
// //      ['koulibali', 'Kalidou', '2020999959240', 'Thies', '2000-08-08', 'cni_2.jpg']
//     ];

//     $stmt = $this->pdo->prepare("
//     INSERT INTO utilisateur (nom, prenom, cni, lieu_naissance, date_naissance, url_copie_cni)
//     VALUES (?, ?, ?, ?, ?, ?)
// ");


//     foreach ($utilisateurs as &$user) {
//       $filename = $user[5];
//       $localPath = $uploadPath . $filename;
//       if ($filename && file_exists($localPath)) {
//         $upload = $this->cloudinary->uploadApi()->upload($localPath, [
//           'folder' => 'appdaf/cni'
//         ]);
//         $user[5] = $upload['secure_url'];
//       } else {
//         if ($filename) {
//           echo "❌ Image introuvable : $filename\n";
//         }
//         $user[5] = null;
//       }
//       $stmt->execute($user);
//     }


//     echo "✅ Données insérées avec succès.\n";
//   }
// }



// try {
//   $pdo = new PDO('pgsql:host=tinkin;port=55432;dbname=daf_app', USER, PASSWORD);
//   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//   $seeder = new Seeder($pdo);
//   $seeder->seed();

//   echo "Base de données remplie avec succès.\n";
// } catch (\PDOException $e) {
//   echo "Erreur : " . $e->getMessage() . "\n";
// }



class Seeder
{
  private PDO $pdo;
  private Uploader $uploader;

  public function __construct(PDO $pdo, Uploader $uploader)
  {
    $this->pdo = $pdo;
    $this->uploader = $uploader;
  }

  public function seed(): void
  {
    $uploadPath = __DIR__ . '/../public/images/upload/';

    $utilisateurs = [
      ['koulibali', 'Kalidou', '2020999959240', 'Thies', '2000-08-08', 'cni_2.jpg']
    ];

    $stmt = $this->pdo->prepare("
            INSERT INTO utilisateur (nom, prenom, cni, lieu_naissance, date_naissance, url_copie_cni)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

    foreach ($utilisateurs as &$user) {
      $filename = $user[5];
      $localPath = $uploadPath . $filename;

      $url = $this->uploader->upload($localPath, 'appdaf/cni');

      if ($url) {
        $user[5] = $url;
      } else {
        echo "❌ Échec upload ou fichier introuvable : $filename\n";
        $user[5] = null;
      }

      $stmt->execute($user);
    }

    echo "✅ Données insérées avec succès.\n";
  }
}



try {

  $pdo = new PDO('pgsql:host=localhost;port=55432;dbname=app_daf', USER, PASSWORD);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $uploader = new CloudinaryUploader();
  $seeder = new Seeder($pdo, $uploader);
  $seeder->seed();

  echo "Base de données remplie avec succès.\n";
} catch (\PDOException $e) {
  echo "Erreur : " . $e->getMessage() . "\n";
}
