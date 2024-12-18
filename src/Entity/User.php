<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')] // Assurez-vous que le nom de la table est correct
#[UniqueEntity(fields: ['email'], message: 'Le compte avec cet email existe déjà.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_naissance = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $mot_de_passe = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 10)]
    private ?string $code_postal = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 20)]
    private ?string $numero_telephone = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $statut = true;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Subscription::class, orphanRemoval: true)]
    private Collection $subscriptions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BookLoan::class, orphanRemoval: true)]
    private Collection $bookLoans;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BookReview::class)]
    private Collection $bookReviews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RoomReservation::class, orphanRemoval: true)]
    private Collection $roomReservations;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
        $this->bookLoans = new ArrayCollection();
        $this->bookReviews = new ArrayCollection();
        $this->roomReservations = new ArrayCollection();
        $this->statut = true;
        $this->roles = ['ROLE_USER'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): static
    {
        $this->date_naissance = $date_naissance;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }
    public function isAdmin(): bool 
    { 
        return in_array('ROLE_ADMIN', $this->roles);
     }

    public function isBanned(): bool 
    { return in_array('ROLE_BANNED', $this->roles);
     } 

     public function ban(): self 
     { $this->roles[] = 'ROLE_BANNED';
        return $this; 
    } 

    public function unban(): self 
    { 
        $this->roles = array_diff($this->roles, ['ROLE_BANNED']); 
        return $this; }

    public function getPassword(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setPassword(string $mot_de_passe): static
    {
        $this->mot_de_passe = $mot_de_passe;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): static
    {
        $this->code_postal = $code_postal;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;
        return $this;
    }

    public function getNumeroTelephone(): ?string
    {
        return $this->numero_telephone;
    }

    public function setNumeroTelephone(string $numero_telephone): static
    {
        $this->numero_telephone = $numero_telephone;
        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): static
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setUser($this);
        }

        return $this;
    }

    public function getActiveSubscription(): ?Subscription 
    { 
        foreach ($this->subscriptions as $subscription) { 
            if ($subscription->getDateFin() > new \DateTime()) { 
                return $subscription; 
            }
         }
         return null;
     }
    public function removeSubscription(Subscription $subscription): static
    {
        if ($this->subscriptions->removeElement($subscription)) {
            if ($subscription->getUser() === $this) {
                $subscription->setUser(null);
            }
        }

        return $this;
    }

     public function getBookLoans(): Collection 
    { 
        return $this->bookLoans; 
    } 
    public function addBookLoan(BookLoan $bookLoan): static 
    { 
        if (!$this->bookLoans->contains($bookLoan)) {
             $this->bookLoans->add($bookLoan); 
             $bookLoan->setUser($this); 
            } 
            return $this; 
        } 
     public function removeBookLoan(BookLoan $bookLoan): static 
        { 
            if ($this->bookLoans->removeElement($bookLoan)) {
                 if ($bookLoan->getUser() === $this) {
                     $bookLoan->setUser(null); 
                    }
                 } 
                 return $this; 
                } 
    public function getRoomReservations(): Collection 
    { 
        return $this->roomReservations; 
    } 
    public function addRoomReservation(RoomReservation $roomReservation): static 
    {
         if (!$this->roomReservations->contains($roomReservation)) { 
            $this->roomReservations->add($roomReservation); 
            $roomReservation->setUser($this); 
        } 
        return $this; 
    } 
    public function removeRoomReservation(RoomReservation $roomReservation): static
     { 
        if ($this->roomReservations->removeElement($roomReservation)) {
             if ($roomReservation->getUser() === $this) {
                 $roomReservation->setUser(null);
                 }
                 }
                  return $this; 
    }

    public function getBookReviews(): Collection
    {
        return $this->bookReviews;
    }

    public function addBookReview(BookReview $bookReview): static
    {
        if (!$this->bookReviews->contains($bookReview)) {
            $this->bookReviews->add($bookReview);
            $bookReview->setUser($this);
        }

        return $this;
    }

    public function removeBookReview(BookReview $bookReview): static
    {
        if ($this->bookReviews->removeElement($bookReview)) {
            if ($bookReview->getUser() === $this) {
                $bookReview->setUser(null);
            }
        }

        return $this;
    }

    

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
