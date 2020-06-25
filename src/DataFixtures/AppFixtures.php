<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Type;
use App\Entity\Rdv;
use App\Entity\Creneau;
use Faker;
use Symfony\Component\Validator\Constraints\DateTime;

class AppFixtures extends Fixture
{
    const NB_PRACT = 1;
    const NB_PATIENT = 10;
    const ADDRESS =
        [
      "4 Rue Sœur Bouvier, 69005 Lyon",
      "1 Montée de Choulans, 69005 Lyon",
      "17 Quai Joseph Gillet, 69316 Lyon",
      "100 Rue des Granges, 69890 La Tour-de-Salvagny",
      "Chemin du Vert Galant, 69170 Tarare",
      "65 Rue du 11 Novembre, 69550 Amplepuis",
      "40 Rue de Belfort, 69550 Amplepuis",
      "27 Rue Pierre Brossolette, 69210 L'Arbresle",
      "800 Route de France, 69210 Fleurieux-sur-l'Arbresle",
      "1150 Route de Sain-Bel, 69280 Marcy-l'Étoile",
      "11 Rue Jules Verne, 69630 Chaponost",
      "165 Chemin du Grand Revoyet, 69310 Pierre-Bénite",
    ];

    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create('fr_FR');
        // standalone fixtures Type  and creneau
        $typeDoctor = new Type();
        $typeDoctor->setTitle("Doctor");
        $typePatient = new Type();
        $typePatient->setTitle("Patient");
        $manager->persist($typeDoctor);
        $manager->persist($typePatient);
        $this->addReference("type_doctor", $typeDoctor);
        $this->addReference("type_patient", $typePatient);
        $creneauMatin = new Creneau();
        $creneauAm = new Creneau();
        $creneauMatin->setTitle("Matin");
        $creneauAm->setTitle("Aprés-Midi");
        $manager->persist($creneauMatin);
        $manager->persist($creneauAm);
        $this->addReference("cr_matin", $creneauMatin);
        $this->addReference("cr_am", $creneauAm);

     
        
        // doctor creation
        for ($i = 0; $i < $this::NB_PRACT; $i++) {
            $aPractician = new  User();
            $aPractician->setFirstName("A.");
            $aPractician->setLastName("Doctor$i");
            $aPractician->setType($this->getReference("type_doctor"));
            $aPractician->setAdress("22 Rue Seguin, 69002 Lyon");
            $this->addReference("practitian_$i", $aPractician);
            $manager->persist($aPractician);
        }

        // patients creation
        for ($i = 0; $i < $this::NB_PATIENT; $i++) {
            $aPatient = new  User();
            $aPatient->setFirstName($faker->firstName);
            $aPatient->setLastName($faker->lastName);
            $aPatient->setType($this->getReference("type_patient"));
            $aPatient->setAdress($this::ADDRESS[$i]);
            $this->addReference("patient_$i", $aPatient);
            $manager->persist($aPatient);
        }

        // meet creation
        for ($i = 0; $i < $this::NB_PATIENT; $i++) {
            $aRdv =new Rdv();
            //  $aRdv->setAdress("");
            $aRdv->setDate($faker->dateTime);
            $aRdv->setIsActive(true);
            $aRdv->setRdvOrder($i);
            $aRdv->setMessage($faker->realText());
            $aRdv->setPatient($this->getReference("patient_$i"));
            // binary switch for set practionner or not 
            if(rand(0,1)){
                $aRdv->setPractitioner($this->getReference("practitian_0"));
                if(rand(0,1)) {
                    $aRdv->setCreneau($this->getReference("cr_matin"));
                } else {
                    $aRdv->setCreneau($this->getReference("cr_am"));
                }
            }
            $manager->persist($aRdv);
        }
        $manager->flush();
    }
}