import { Button, ButtonGroup } from "@mui/material";
import { Icon } from '@iconify/react';

export default function UserActions() {
  return (
    <>
      <ButtonGroup>
        <Button variant="text">
          <Icon icon="mdi:eye"/>
        </Button>
        <Button variant="text">
          <Icon icon="mdi:lead-pencil"/>
        </Button>
        <Button variant="text">
          <Icon icon="mdi:cancel"/>
        </Button>
      </ButtonGroup>
    </>
  );
}
