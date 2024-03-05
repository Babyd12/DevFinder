<?php

namespace App\Services; 

use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;

class SendMailService
{
    public $defaultFromEmail = 'mamadcir24@gmail.com';
    public $subjectMotDepasseOublie = 'Réinitialisation de votre mot de passe';
    public $subjecRecrutementApprenant = 'Opportunité d\'emploi';
    public $subjectCongedierApprenant = 'Fin de votre participation à notre programme';

    public $templateMotDepasseOublie = 'mot_de_passe_oublie';
    public $templateRecrutementEntreprise = 'rectutement_entreprise';
    public $templateCongedierApprenant = 'congedier_apprenant';

    public $messageRetourMotDePasseOublie = 'Un e-mail de réinitialisation de mot de passe a été envoyé. 
    Veuillez vérifier votre boîte de réception ou courrier indésirable si nécessaire.';

    public function __construct(private MailerInterface $mailer){}

    public function sendEmail(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void {
        //On crée le mail
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("emails/$template.html.twig")
            ->context($context);

        // On envoie le mail
        $this->mailer->send($email);
    }

    public function defaultFrom()
    {
        return $this->defaultFromEmail;
    }

    public function getSubjectMotDepasseOublie(): string
    {
        return $this->subjectMotDepasseOublie;
    }

    public function getSubjectRecrutementApprenant(): string
    {
        return $this->subjecRecrutementApprenant;
    }

    public function getSubjectCongedierApprenant(): string
    {
        return $this->subjectCongedierApprenant;
    }

    public function getTemplateMotDepasseOublie(): string
    {
        return $this->templateMotDepasseOublie;
    }

    public function getTemplateRecrutementEntreprise(): string
    {
        return $this->templateRecrutementEntreprise;
    }

    public function getTemplateCongedierApprenant(): string
    {
        return $this->templateCongedierApprenant;
    }

    public function getMssageRetourMotDePasseOublie(): string
    {
        return $this->messageRetourMotDePasseOublie;
    }
}
