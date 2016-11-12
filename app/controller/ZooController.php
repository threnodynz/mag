<?php
namespace Controller;
use Model\Animal;
use Model\Monkey;
use Model\Giraffe;
use Model\Elephant;
use Common\Helper;
use DateTime;
use DateInterval;

class ZooController
{
    protected $content = array();

    /** @var \View\HTML */
    protected $view;

    /** @var  \Base */
    protected $f3;


    public function __construct( \Base $f3 )
    {
        $this->f3 = $f3;

        // Check all the required parameters are part of the current PHP session and if not create them.
        if (!$this->f3->exists('SESSION.zoo.dateTime') || !$this->f3->exists('SESSION.zoo.animals') ||
            !is_array($this->f3->get('SESSION.zoo.animals')) || empty($this->f3->get('SESSION.zoo.animals'))) {

            $this->f3->set('SESSION.zoo.dateTime', (new DateTime('00:00:00'))->format('H:i:s'));
            $this->f3->set('SESSION.zoo.animals', []);

//            $this->createAnimal('Monkey', 5);
//            $this->createAnimal('Giraffe', 5);
            $this->createAnimal('Elephant', 5);
        }

    }

    public function listAnimals()
    {
        $this->content['content'] = $this->f3->get('SESSION.zoo');
    }

    private function createAnimal($type = 'Animal', $count = 1)
    {
        $group = $type;
        $group = lcfirst($group);

        // Check the expected array nodes exist.

        if (!isset($_SESSION['zoo']['animals'][$group]) || !is_array($_SESSION['zoo']['animals'][$group])) {
            $_SESSION['zoo']['animals'][$group] = [];
        }

        for($i = 1; $i <= $count; $i++) {
            // Call the static factory method for this type of animal object.
            $animal = call_user_func(
                [
                    'Model\\' . $type,
                    'factory'
                ]
            );

//            $animal = Animal::factory('monkey');
//            var_dump($animal);

            $_SESSION['zoo']['animals'][$group][] = $animal;

        }

    }


    /**
     * init the view
     * @param \Base $f3
     */
    function beforeroute( \Base $f3 )
    {
        if ($f3->get('AJAX'))
            $this->view = new \View\JSON();
        else
            $this->view = new \View\HTML();
    }


    /**
     * feed the view and squeeze it out
     * @param \Base $f3
     */
    function afterroute( \Base $f3 )
    {
        $this->view->setData($this->content);
        echo $this->view->render();
    }


    public function elapseTime()
    {
        $oneHour = new DateInterval('PT1H'); // One hour time interval.

        $currentDateTime = $this->f3->get('SESSION.zoo.dateTime');
        $addedOneHour = (new DateTime($currentDateTime))->add($oneHour);

        $addedOneHour = $addedOneHour->format('H:i:s');

        $this->f3->set('SESSION.zoo.dateTime', $addedOneHour);

        $sessionAnimals = $this->f3->get('SESSION.zoo.animals');

        foreach($sessionAnimals as $group => $animals) {
            foreach($animals as $index => $animal) {
                $count = rand (0, 20); // Generate health reduction for each animal.

            if ($animal->state !== Helper::STATE_DEAD) { // Dead animals generally stay dead.
                    $animal->decreaseHealth($count);
                }
            }
        }

        $this->f3->reroute('/list');
    }

    public function feedAnimals()
    {
        $sessionAnimals = $this->f3->get('SESSION.zoo.animals');

        $usedRandomValues = [];

        foreach($sessionAnimals as $group => $animals) {

            // Generate three random values between 10 and 25, one for each type of animal.
            do {
                $rand = rand(10, 25); // Generate health increase for each type of animal.
            } while (in_array($rand, $usedRandomValues));


            foreach ($animals as $index => $animal) {
                if($animal->state !== Helper::STATE_DEAD) { // You can't feed a dead animal.
                    $animal->increaseHealth($rand);
                }
            }
        }

        $this->f3->reroute('/list');
    }

    public function resetSim() {
        $this->f3->set('SESSION.zoo', []);

        $this->f3->reroute('/list');
    }
}